# iinano
A social network that follows SOLID -- **Work in progress**

## What
It's a **private social network** web application for small communities/business.
Built on Symfony2 & MongoDB, it is an attempt to keep things small, light, simple
and intuitive.

The model consists of less than 150 NCLOC of PHP and the main bundle (excluding symfony and
the dbal) is about 2000 NCLOC of PHP. There's a lot of twig of course, but they are
dumb templates (only about 25 "ifs" in those templates).

The model is smart enough to keep its integrity by itself, controllers are dumb
and lean, repositories are business-relevant and forms are very constrained.
There is a double security on routes and on repositories.

Since it's a private social network, there is no SEO at all, but you can add
the google tracking.

## Why
Because after struggling with Elgg (almost dead) and Oxwall (coding horror) and
reviewing the new Idno (critical design flaws and no unit tests),
I've decided to NOT contribute to Idno (sadly) but to retain the philosophy of these
kind of small network and write an app from scratch.

## How
Four rules to bind them all :

* SOLID or die
* Keep It Simple Stupid
* Fast for both client and server sides
* Lightweight for both client and server sides

Therefore : no SQL, no ORM, no annotations, no fancy JS framework, no Twitter Bootstrap,
no NodeJS.

So I kept the minimal requirements :

* server side : Symfony2 (lightened to 5 bundles) and MongoDB (check out the composer.json)
* client side : jQuery and Yahoo PureCSS and some ultra-light jQuery plugins.

And of course mandatory stuffs like Composer, PhpUnit and phpDocumentor

## Who
Small business, small communities (from 100 to 10k people).
Everything is public in the community and you can filter contents by your following,
your followers and your friends. A friend is a following and a follower.

The customer or the community leader, wants to own his data ! If he's a manager, he's fed up
with his employees sharing critical business data and secrets
on public full-open social networks since on a regular basis, facebook
has security issues or policy changes which put all contents in free access
for everyone.

Another use-case is a community leader who is lost in the noise generated
by those public networks : with iinano, there's no ads, no spams, no game, just
content, clean and clear.

## Where
AWS of course !

## FAQ

### Y U NO use &lt;insert your favorite piece of software here&gt; ?
Well, there's a good chance I've tried it and I rejected it because it was (choose one) :

* too big
* too slow
* poorly coded/tested
* poorly documented/supported

For example, I've started this app with the famous twitter bootstrap CSS framework.
Rapidly, the weight of this framework (and its required verbose html) drives me
to switch to Foundation. When the design was almost finished, I restart the design
from scratch with PureCSS because of performance issue with its js components.

I have tried AngularJS, that was cool but, apart its difficulty to "mingle" with Twig
I drop it out because of its heavy weight and the vendor lock-in. And I had concerns about
security in a RESTful config.

I've tried multiple javascript micro-frameworks like riot.js, soma.js and
director.js, no way to be SOLID at maximum level (mainly because of javascript
design flaw) or to fall in the vendor lock-in. After that, I decide to keep
all js stuff at the minimum level. A thing to remember: one kilobyte of javascript
is one millisec of latency on client-side.

I've thought to replace Symfony2 by Silex but the benefit would be unclear when
you add a lot of Providers, furthermore the silex security provider is a
"pain in the ass" to fine-tuned.

For performance concerns, I drop out the validation component in the model and
design it to be constrained in constructors.

I've tried to store pictures on S3 but the signature system V4 is not what
I want for maximum security and the read-write consistencies is difficult to
manage with. Perhaps in the future with a full-stack Amazon solution (with CouldFront)

### How to make money if there's no ads ?

First, there's no ways to pay your Amazon server with only advertising,
unless you have millions of users (at least). Second, Adblock seems to be
more and more popular each day. Third, I'm currently developing a system of entrance
fee with Paypal.

### What about connection between iinano and &lt;insert your favorite social network&gt; ?

Currently facebook and twitter are only required for authenticating users
and there's no plan to add more bridges. I think people who pay for privacy on
a private social network don't want to mix public life and private life. So,
wait and see...

### What about the cloud ?

I hope the next release of iinano will run exclusively on EC2, S3, DynamoDB
and CouldFront.

### I've seen the model and the database content is denormalized, why ?

It's on purpose. My prerequisites was that front page must not require more
than 3 queries (including the mandatory query for the logged user).

The drawback is there are some esoteric mapreduces
but it's a feature of MongoDB and V8 javascript is a modern language
unlike the good thirty-year-old SQL.

## Team

* Lead developer : [Florent Genette](https://github.com/Trismegiste)