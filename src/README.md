# More about WP GraphQL

The src folder will greatly be expanding as we build the WP GraphQL
implementation out. The most important thing here are the types, but eventually
different queries, mutations and subscriptions will be built.

## TypeSystem

The type system is fundamental to GraphQL. It is basically an object that holds
every type available within the system. To learn more about types view the
[GraphQL spec](https://facebook.github.io/graphql/). And ready more in the
README.md for the Type folder.

There are some top level types known as initial types: query and mutation. Query
is for read only operations and is an object type build out of our type system.
It is extremely, extremely important that our read only functionality does not
cause side effects and like rest it should be idempotent, a fancy word for
"doesn't change the state of the application given the same request."
Mutations are used for the opposite of queries. Mutations should have side
effects but only one side effect per query. This way the change of our WordPress
state is easily tracked. A good example of a mutation would be changing the post
content for a post. Alternatively, it another type within our initial mutation
type could create a post. It is important to understand that query and mutation
are built out of individual types from our type system.

## Context

The app context is useful for storing some request specific information. The
application context will become the way of determining what user is currently
authenticated for a request for whom we can give proper authorization to various
fields.

This is an area that will evolve a lot.
