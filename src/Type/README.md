# Types

Types are fundamental to GraphQL, they form the building blocks of the schema.
A schema is data that tells us the shape our data will take. For instance the
PostType may have a bunch of fields, each field will have its own type, maybe a
string maybe something else. The PostType will itself be a type and the queries
we use to find our posts are also types.

Almost everything becomes a type and the relations of our data are defined
through these different types. Types can wrap around other types in a
compositional fashion. Let's breakdown some of the types in GraphQL.

## Scalars

Scalars are types that do not contain additional fields and resolve to only one
value. Due to the text based nature of a GraphQL query any scalar value could
potentially be written as a string. It is wise to leverage the internal types
that a language affords. This is a PHP implementation of GraphQL for WordPress,
so in this plugin we will make heavy use of the PHP types like string, int, and
boolean.

It is important to note that more sophisticated scalar types can be created as
well. For instance, WordPress has a lot of HTML content. This content could be
represented simply by a string but we could create a HTML type that would handle
the validation and sanitization of any field set to the HTML type. This enables
us to have more fine grained control than just to consider the html content
another string.

## Objects

Object types are used to group fields. An object type must have at least one
field. Any field within an object will have its own type defined as well. Object
types are where things get interesting and where the overall schema/structure of
our application starts to take shape. In the context of WordPress the schema is
pretty well laid out for us already. The goal of this project is to find an
elegant way to expose that schema as a GraphQL type system.

Object types can also take arguments in the GraphQL query. This enables for a
wide range of possibilities and flexibility when querying.

Let's think of some things in WordPress that fit the Object Type bill. The first
that comes to my mind is `WP_Post`. `WP_Post` is a PHP object that describes the
various attributes of a WordPress post. In WordPress, posts are kind of
confusing as there are many post types. To match WordPress's schema more
precisely we will want a `PostInterfaceType` that will cover some of the shared
fields among each post type, then for every post type an individual object type
should go along side that. Let's talk more about interface types.

## Interfaces

Interface types are used to define a set of fields that can be implemented by
an object type. If you are familiar with the interface keyword in PHP, Java, or
other languages you will have a general idea of what interface types bring to
GraphQL.

If you are not familiar with the interface keyword, think of an
interface as a description or tool describing how to interact with an object.
The interface will enable you to interact with the object without knowing the
magic that goes on behind the scenes, similar to how a keyboard describes how we
can enter text into a computer without us having to worry about the technical
details.

Interfaces in Object Oriented Programming languages provide a hacky way to get
multiple inheritance. What does that mean? With classical inheritance every
object inherits from one parent object and then extends that functionality. You
can't combine two classes into one like Mammal and Egg Layer to get a Platypus
or can you? With interfaces you can define what an object should do or how it
interacts with things. There could be a Mammal interface and an Egg Layer
interface that say "I do mammal things" and "I do egg laying things". Then a
Platypus type could come along and easily implement both of these qualities.
Nature, despite the grand hierarchical classification that we often view it
through, favors composition over inheritance. Each one of us is composed of
roughly **37.2 TRILLION CELLS** and about **50 TRILLION** microbial organisms.
Each cell of ours is composed of roughly **100 TRILLION ATOMS**; WOW! Imagine,
trying to represent that with some sort of classical hierarchy; it ain't gonna
happen. This is why object composition and multiple inheritance are such
important concepts to understand when dealing with software architecture.

GraphQL allows us to bake multiple inheritance and object composition directly
into our type schema. Interfaces allow for multiple inheritance among types and
object composition is created through the building upon of basic types. In
WordPress there are many opportunities to leverage Interface types to build our
GraphQL schema. A PostInterfaceType for common post type fields would be great.
This means that instead of redefining these fields over and over and over again
in every post type object we could instead implement the PostInterfaceType for
each post.

## Unions

Union Types are somewhat tricky. Union types are used to represent a list of
possible object types, without guaranteeing that fields will be consistent
across types. Because there is no guarantee of what fields are present when
querying for a union type you cannot define fields within the union type like
you normally would for other queries. Instead you are forced to use fragments
to determine which fields you want to return for the various object types being
joined by the union type.

Confusing!? I think it is. For WordPress, I only see this being applicable for
situations where you would want to be able to do advanced searching queries.
As we dive deeper into the project, more applications for leveraging union types
will most likely arise.

## Enums

Enums or enumerable types are a special form of Scalar types. Enumerable types
are pretty straightforward; they provide a list of possible scalar values. Each
value should be unique. For WordPress there are a huge number of possible
enumerable types. Post statuses, post types, taxonomies, image sizes, and many
many more.

## Lists

List types are types that define what a collection is made up of. Lists can be
used to define lists of scalars and enums, but they can also be used to define
lists of object types. Let's look at a WordPress example. Say we have a Posts
Type it will most likely be a ListType of individual Post ObjectTypes. Pretty
neat! You can probably now see how all of these things come together.

## NonNull

NonNull Types are also pretty straightforward; they define whether a field can
hold an null value (empty value). This type must be implemented by wrapping
around another base type.

In WordPress a perfect non null example would be an ID field for some resource
like a post. So a Post Types id field would have a nonnull type of a string.

## Input Objects

Fields can define various arguments to pass to them while querying. Most of the
time these arguments will be a simple scalar value like a string or integer.
Sometimes the need for a more complex structure of input is needed; enter Input
Objects!

With WordPress a great use case of an input object would be query parameters for
WP_Query. By using an Input Object we could set default values, validation, and
create a near replica of WP_Query. Input objects are really great and I think
WordPress will have a lot of need for these kind of types.

## That's a wrap!

That's a basic overview of Types. Understanding types and how to combine them is
essential to creating a strong and usable GraphQL schema for powerful querying.
