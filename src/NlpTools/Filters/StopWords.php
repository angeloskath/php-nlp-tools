<?php
namespace NlpTools\Filters;
use NlpTools\Utils\Interfaces\TokenTransformationInterface;

/**
 * Stop words is an english list of stop words
 * @author Dan Cardin (yooper)
 */
class StopWords implements TokenTransformationInterface
{    
    /**
     * An array of stop words
     * @var array 
     */
    protected $stopWords = null;
    
    /**
     * Initializes list of stop words 
     */
    public function __construct()
    {
        $this->initStopWords();
    }
    
    /**
     * searches through the stop list, if the word is in the stop list return a null
     * implements a binary search since the stop words have been pre-sorted
     * @param string $token 
     */
    public function transform($token)
    {
        $right = count($this->stopWords) - 1;
        $left = 0;
        while($right >= $left) 
        {
            $pivot = floor(($left + $right) / 2);
            if (strcmp($this->stopWords[$pivot], $token) < 0){
                $left = $pivot + 1;
            } elseif (strcmp($this->stopWords[$pivot], $token) > 0) { 
                $right = $pivot - 1;
            } else  { 
                return null; // token was found in stop list
            }
        }
        return $token; //token was not found in stop list.
    }
    
    /**
     * init a list of stop words 
     */
    protected function initStopWords()
    {
        $stopWords =<<< STOPWORDS
a
able
about
above
according
accordingly
across
actually
after
afterwards
again
against
ain\'t
all
allow
allows
almost
alone
along
already
also
although
always
am
among
amongst
amoungst
amount
an
and
another
any
anybody
anyhow
anyone
anything
anyway
anyways
anywhere
apart
appear
appreciate
appropriate
are
aren\'t
around
as
a\'s
aside
ask
asking
associated
at
available
away
awfully
back
be
became
because
become
becomes
becoming
been
before
beforehand
behind
being
believe
below
beside
besides
best
better
between
beyond
bill
both
bottom
brief
but
by
call
came
can
cannot
cant
can\'t
cause
causes
certain
certainly
changes
clearly
c\'mon
co
com
come
comes
computer
con
concerning
consequently
consider
considering
contain
containing
contains
corresponding
could
couldnt
couldn\'t
course
cry
c\'s
currently
de
definitely
describe
described
despite
detail
did
didn\'t
different
do
does
doesn\'t
doing
done
don\'t
down
downwards
due
during
each
edu
eg
eight
either
eleven
else
elsewhere
empty
enough
entirely
especially
et
etc
even
ever
every
everybody
everyone
everything
everywhere
ex
exactly
example
except
far
few
fifteen
fifth
fify
fill
find
fire
first
five
followed
following
follows
for
former
formerly
forth
forty
found
four
from
front
full
further
furthermore
get
gets
getting
give
given
gives
go
goes
going
gone
got
gotten
greetings
had
hadn\'t
happens
hardly
has
hasnt
hasn\'t
have
haven\'t
having
he
hello
help
hence
her
here
hereafter
hereby
herein
here\'s
hereupon
hers
herse”
herself
he\'s
hi
him
himse”
himself
his
hither
hopefully
how
howbeit
however
hundred
i
i\'d
ie
if
ignored
i\'ll
i\'m
immediate
in
inasmuch
inc
indeed
indicate
indicated
indicates
inner
insofar
instead
interest
into
inward
is
isn\'t
it
it\'d
it\'ll
its
it\'s
itse”
itself
i\'ve
just
keep
keeps
kept
know
known
knows
last
lately
later
latter
latterly
least
less
lest
let
let\'s
like
liked
likely
little
look
looking
looks
ltd
made
mainly
many
may
maybe
me
mean
meanwhile
merely
might
mill
mine
more
moreover
most
mostly
move
much
must
my
myse”
myself
name
namely
nd
near
nearly
necessary
need
needs
neither
never
nevertheless
new
next
nine
no
nobody
non
none
noone
nor
normally
not
nothing
novel
now
nowhere
obviously
of
off
often
oh
ok
okay
old
on
once
one
ones
only
onto
or
other
others
otherwise
ought
our
ours
ourselves
out
outside
over
overall
own
part
particular
particularly
per
perhaps
placed
please
plus
possible
presumably
probably
provides
put
que
quite
qv
rather
rd
re
really
reasonably
regarding
regardless
regards
relatively
respectively
right
said
same
saw
say
saying
says
second
secondly
see
seeing
seem
seemed
seeming
seems
seen
self
selves
sensible
sent
serious
seriously
seven
several
shall
she
should
shouldn\'t
show
side
since
sincere
six
sixty
so
some
somebody
somehow
someone
something
sometime
sometimes
somewhat
somewhere
soon
sorry
specified
specify
specifying
still
sub
such
sup
sure
system
take
taken
tell
ten
tends
th
than
thank
thanks
thanx
that
thats
that\'s
the
their
theirs
them
themselves
then
thence
there
thereafter
thereby
therefore
therein
theres
there\'s
thereupon
these
they
they\'d
they\'ll
they\'re
they\'ve
thick
thin
think
third
this
thorough
thoroughly
those
though
three
through
throughout
thru
thus
to
together
too
took
top
toward
towards
tried
tries
truly
try
trying
t\'s
twelve
twenty
twice
two
un
under
unfortunately
unless
unlikely
until
unto
up
upon
us
use
used
useful
uses
using
usually
value
various
very
via
viz
vs
want
wants
was
wasn\'t
way
we
we\'d
welcome
well
we\'ll
went
were
we\'re
weren\'t
we\'ve
what
whatever
what\'s
when
whence
whenever
where
whereafter
whereas
whereby
wherein
where\'s
whereupon
wherever
whether
which
while
whither
who
whoever
whole
whom
who\'s
whose
why
will
willing
wish
with
within
without
wonder
won\'t
would
wouldn\'t
yes
yet
you
you\'d
you\'ll
your
you\'re
yours
yourself
yourselves
you\'ve
zero

STOPWORDS;
        
        //there is an extra new line that must be popped off
        $this->stopWords = explode(PHP_EOL, $stopWords);        
        array_pop($this->stopWords);
        
    } // end of init stop words
}

