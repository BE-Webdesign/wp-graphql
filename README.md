# Hello and Welcome to WP GraphQL!

The project aims to create a full blown GraphQL API for WordPress. It uses
[WebOnyx's PHP port of the GraphQL reference implementation.](https://github.com/webonyx/graphql-php)
Eventually, I hope we can backport this project to PHP 5.2 so everyone can enjoy
GraphQL on their WordPress install.

## Disclaimer

This project is in alpha and will be changing a lot very rapidly at times. It is
currently insecure as well as it will expose private information for an install
since there is no authentication/authorization in place yet. **DO NOT USE THIS
IN PRODUCTION AS THIS PROJECT IS NOT MEANT FOR THAT YET**. When the project goes
to beta it will signify a certain confidence in the stability and security of
this plugin. Phew! Now onto the good stuff!

## Installing

### Requirements

WordPress 4.6+ (honestly it probably works on much earlier versions.)
PHP 5.4+ (this is a must cut off as the library this project uses is 5.4+)
Composer (Composer is required to install the dependencies for this plugin)

#### WordPress Setup

[Download WordPress:](https://wordpress.org/download/)
You can download WordPress manually and follow the wonderful instructions on
.org to figure out how to get a [WordPress development environment up and running.](https://developer.wordpress.org/themes/getting-started/setting-up-a-development-environment/)

[VVV:](https://github.com/Varying-Vagrant-Vagrants/VVV)
If you are a more experienced user or want to try something new, I highly recommend
VVV. VVV comes with all of the tools you need. Essentially you install [Vagrant](https://www.vagrantup.com/)
and a VM. I like [VirtualBox VM](https://www.virtualbox.org/) but Vagrant
supports many others.

[Chassis:](https://github.com/Chassis/Chassis)
Chassis is along the same line as VVV, but is more modularized. It requires more
set up than VVV in some ways but, is faster to boot and sometimes you don't need
all of the bells and whistles, and if you do well there are some awesome
extensions for Chassis.

Once you have WordPress installed and running you will want to clone this git
repository or download the zip and install it in your WordPress installs plugins
directory. [Here is a great guide for installing plugins.](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)
This plugin is not yet ready for the WordPress.org plugin repository but one of
the goals for this project is to make it compatible.

#### Using Composer

Composer comes with things like VVV and is why VVV is a great choice for running
a developer environment. Composer is basically a tool that helps manage packages
and dependencies for PHP projects. Composer is used for three things on this
plugin: load WebOnyx GraphQL library, install PHPUnit for unit testing, and
finally it will auto-load all of the necessary files for the plugin.

When you have composer running on your developer environment you will want to
locate the directory for wp-graphql. Once you are in the wp-graphql directory
you will want to enter:

```
composer install
```

This will install all of the dependencies for the project and add a vendor
folder to the project containing these dependencies. When adding new files you
may notice that they are not loading into the project. If you follow the
namespaces already set up, you can easily load the new file by running:

```
composer dump-autoload
```

This will regenerate all of the auto loaded files bringing in the ones that are
missing. The top level namespace is `BEForever\WPGraphQL`.

#### The plugin should now be functional!

### Using WP GraphQL

You can send GraphQL requests like you would any old AJAX requests but there is
an incredibly awesome tool GraphiQL that has been created by Facebook. If you
want to play around in GraphQL there is no better way at the moment, so GraphiQL
comes highly recommended. To use GraphiQL for PHP install this [chrome extension; ChromieQL](https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij)

You will want to set the endpoint area to `http://local.mywordpress.dev/graphql`.
Basically whatever url you are using for your WordPress install and `/graphql`.

All this is really doing is sending the GraphQL query string as a HTTP request
data which is then parsed by this plugin and executed by the GraphQL plugin.

#### Supported Queries

Currently there are only three types we can query: post, user, and hello.

Lets look at what an example GraphQL query will look like for each.

##### Hello

A query for hello will look like this:

```
{
  hello
}
```

and will return:

```
{
  "data": {
    "hello": "Welcome to WP GraphQL, I hope that you will enjoy this adventure!"
  }
}
```

##### Post

A query for post with `ID === 1` might look like this:
```
{
  post(id: 1) {
    title
    content
    date
  }
}
```

and will return something like this:

```
{
  "data": {
    "post": {
      "title": "Hello world!",
      "content": "TestingTestinTesting",
      "date": "2016-01-13 02:26:48"
    }
  }
}
```

Notice how only the fields we queried for on that post object type were returned
this is one of the key powers of GraphQL. It is fully declarative and makes
getting the data you need a breeze. The fields for an object type can be queried
by the __schema query or by using the handy introspection tool on the right hand
side of GraphiQL.

##### User

A query for user with `ID === 1` might look like this:
```
{
  user(id: 1) {
    email
  }
}
```

and will return something like this:

```
{
  "data": {
    "user": {
      "email": "graphqliscool@withwordpress.luv"
    }
  }
}
```

##### Combining Queries

We can do all three at once like this.

```
{
	hello
	post(id: 1) {
		title
		content
	}
	user(id: 1) {
		email
	}
}
```

and our JSON response will include all three.

More information to come!

## Roadmap

A loose plan in an order that I think makes sense: feel free to chime in with
your thoughts! I am probably missing some important items as well.

1. Get object types for single resources set up and in good shape. Post, User,
Taxonomy, Term, Comment, Pingback, Theme, Plugin, etc. Don't worry about authorization yet.
2. Improve the infrastructure around serving requests.
3. Implement Authentication for WP GraphQL most likely Basic Auth at first, so
proper authorization can be built out for the project and security will be in a
decent starting place.
4. Get collection types implemented. Then build special query types as well to mirror
WP_Query and the various flavors of it.
5. Start backporting the library and plugin to PHP 5.2 if necessary ( maybe WordPress will have version bumped! ).
6. Do a thorough audit and see if it is ready for the WordPress.org plugin repository.

Things that will happen at all points in project:

Improve TypeSystem/Schema, Improve Documentation, Improve Test Suite. For every
improvement/change we will want test coverage and documentation coverage. By
bonding documentation to testing to the actual changes being made we can create
a very clean final product.

### Documentation

Inline documentation is great and should be used. Especially when tricky
things are going on in the code that someone may be unfamiliar with. They can
also be used to describe the intent of the code and document any decisions being
made.

For project level documentation, details should be placed into the respective
README.md files so that users of this GitHub repo can quickly view things.
Eventually, either a GitHub pages site will be created, or I can spin up a WordPress
site to host the documentation on. It would be really cool to use WordPress and
test out the GraphQL API on the documentation site.

### Test Coverage

We use [PHPUnit](https://phpunit.de/index.html) for our unit testing. PHPUnit will be installed when you run
composer. The tests are located in the `tests` directory of this plugin. To run
the test suite, find the wp-graphql directory via your command line and type in

```
phpunit
```

A cool little test will run in your command line, and it will display error
reporting if anything failed. If you are using VVV, phpunit will already be
installed. To run the suite against multisite type in the following.

```
phpunit -c multisite.xml
```

This will load the configuration file for a multisite test suite.

## Code of Conduct

Be kind, polite, and respectful of others. Have fun!
