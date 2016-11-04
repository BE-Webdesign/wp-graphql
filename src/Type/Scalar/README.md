# Scalar Types

Scalar types are used to represent scalar values.

## Description

A scalar type will have no subsequent fields. Enumerable types are just a
special kind of scalar type. A great way to think about scalar types would be
language primitives. If we had an ObjectType of Burrito, maybe it would contain
the fields: hasBeans, hasRice, hasGreens, hasTomatoes. Each of these fields
would resolve to a boolean value of True or False. Strings, integers, floats,
numbers, and any other scalar values fit into this category of types. If a field
is of a scalar type then it will resolve to only one basic value. Most if not
all complex types are built out of individual scalars, in many ways they are the
building blocks for a high level type system.
