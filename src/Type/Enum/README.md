# Enumerable Types

This section is reserved for enumerable types aka EnumTypes.

## Description

Enumerable types are used to specify types whose value can only match a limited
set of predefined values. For instance you might create an enumerable type for
periods in "Classical Music". Our EnumType could have Baroque, Classical, and
Romantic all as values. Then our Composer ObjectType could have a field named
era. That era field would be an instance of EraEnumType, meaning its value could
only match Baroque, Classical, or Romantic. You can see that is kind of limiting
but that is the whole point of EnumTypes. If you need to limit values to a
certain set, create an EnumType.
