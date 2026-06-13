#! /usr/bin/perl
use Cwd qw( getcwd abs_path );
use List::Util qw( any none );
use Data::Dumper;

print "Creates a Dockerfile and a Makefile to build a Docker container for the FS DBTools GUI for a given registration repository.\n\n";
print "Run make after running this script.\n";

my $port = get_free_port();

write_dockerfile( $port );
write_makefile( $port );

# ============================================================
sub get_free_port {
# ============================================================
	my $ps     = [ split /\n/, `docker ps` ];
	my $header = [ map { s/\W/_/g; lc } split /\s\s+/, shift @$ps ];
	my $containers  = [];

	# ===== LIST CONTAINERS
	foreach (@$ps) {
		chomp;
		my @values    = split /\s\s+/;
		my $container = { map { $_ => shift @values } @$header };
		my $ports     = [];
		foreach my $port (split /, /, $container->{ ports }) {
			my ($info, @ipaddr) = reverse split /:/, $port; $ipaddr = join( ':', reverse @ipaddr );
			my ($host, $container, $protocol) = split /(?:\->|\/)/, $info;
			my ($min, $max) = split /\-/, $host;
			$max = $max ? $max : $min;

			foreach my $port ($min .. $max ) {
				push @$ports, {
					host => $port,
					container => $container,
					protocol => $protocol,
					ipaddr => $ipaddr
				};
			}
		}
		delete $container->{ ports };
		$container->{ port_map } = $ports;
		push @$containers, $container;
	}

	# ===== FIND A FREE PORT
	my $free       = 1;
	my $attempts   = 0;
	my $max        = 100;
	my $port       = 3380;
	
	foreach my $try ( $port .. $port + $max ) {
		$port = $try;

		# The port is free if none of the containers uses that host port
		$free = none {
			my $ports = $container->{ port_map };
			any { $_->{ host } == $port } @$ports;
		} @$containers;

		last if $free;
	}

	return $port;
}

# ============================================================
sub write_dockerfile {
# ============================================================
	my $port = shift;

	open my $fh, '>', 'Dockerfile' or die "Can't create Dockerfile";
	print $fh <<EOF;
FROM php:8.5-apache
EXPOSE $port
WORKDIR /usr/local/tkdu

EOF
	close $fh;
}

# ============================================================
sub write_makefile {
# ============================================================
	my $port    = shift;
	my $path    = `pwd`;
	my $version = 0.5;

	my @paths = split /\//, $path;
	$path = join( '/', splice( @paths, 0, -1 ));

	open my $fh, '>', 'Makefile' or die "Can't create Makefile";
	print $fh <<EOF;
.PHONY: all build run stop clean

all: clean build run

clean:

build:
	docker build -t tkdu:$version .

run:
	docker run -d -p $port:80 --name tkdu-local -v '$path/backend':/usr/local/tkdu -v '$path/frontend':/var/www/html tkdu:$version

stop:
	docker stop tkdu-local || true

clean: stop
	docker image rm tkdu:$version || true

EOF
	close $fh;
}

