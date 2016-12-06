package TKDU::RequestHandler;
use Clone qw( clone );
use Data::Dumper;
use Data::Structure::Util qw( unbless );
use Digest::SHA1 qw( sha1_hex );
use File::Slurp;
use Try::Tiny;

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
		read  => \&handle_read,
		write => \&handle_write,
	}->{ $request->{ action }}( $self, $request );
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
}

1;
