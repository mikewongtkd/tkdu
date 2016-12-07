package TKDU::RequestHandler;
use Clone qw( clone );
use Data::Dumper;
use Data::Structure::Util qw( unbless );
use Digest::SHA1 qw( sha1_hex );
use File::Slurp;
use JSON::XS();
use TKDU::Certificate;
use Try::Tiny;

our $LOGPATH = '/usr/local/tkdu/data';

# ============================================================
sub new {
# ============================================================
	my ($class) = map { ref || $_ } shift;
	my $self    = bless {}, $class;
	$self->init( @_ );
	return $self;
}

# ============================================================
sub init {
# ============================================================
	my $self = shift;
}

# ============================================================
sub handle {
# ============================================================
	my $self    = shift;
	my $request = shift;
	{
		certificate => \&handle_certificate,
		read        => \&handle_read,
		write       => \&handle_write,
	}->{ $request->{ action }}( $self, $request );
}

# ============================================================
sub handle_certificate {
# ============================================================
	my $self    = shift;
	my $request = shift;
	my $cert    = "$TKDU::Certificate::PATH/$request->{ sha1 }.svg";

	die "User certificate '$request->{ sha1 }' not found in database" unless -e $cert;
	return read_file( $cert )
}

# ============================================================
sub handle_read {
# ============================================================
	my $self    = shift;
	my $request = shift;
	my $db      = "/usr/local/tkdu/db/qa/$request->{ subject }.json";

	die "Course subject '$request->{ subject }' not found in database" unless -e $db;
	return read_file( $db )
}

# ============================================================
sub handle_write {
# ============================================================
	my $self    = shift;
	my $request = shift;
	my $name    = $request->{ name };
	my $score   = $request->{ score }/$request->{ count };
	my $subject = uc $request->{ subject };
	my $title   = $request->{ title };

	my $certificate = new TKDU::Certificate( $name, $subject, $title );
	my $code        = $certificate->code();
	my $date        = $certificate->date();
	my $log         = "$LOGPATH/log.txt";

	open  FILE, ">>$log" or die "Can't write to '$log' $!";
	print FILE join( "\t", ($date, $name, $subject, $title, $score, $code )) . "\n";
	close FILE;

	$certificate->write();

	my $json     = new JSON::XS();
	my $response = { certificate => $certificate->svg(), code => $code };
	return $json->encode( $response );
}

1;
