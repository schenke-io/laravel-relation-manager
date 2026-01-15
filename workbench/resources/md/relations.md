# Relationships

## 1. Model relations

| Model | Direct | Indirect |
| --- | --- | --- |
| Capital | country (Country) | location (Location) |
| City | region (Region)<br>tags (Tag) | country (Country)<br>highways (Highway)<br>location (Location) |
| Country | capital (Capital)<br>oldest (City)<br>regions (Region) | cities (City) |
| Highway |  | cities (City)<br>locations (Location) |
| Location |  | locationable (Location) |
| Region | cities (City)<br>country (Country)<br>tags (Tag) | capital (Capital) |
| Single |  |  |
| Tag | cities (City)<br>regions (Region) |  |


## 2. Table relations (Diagram)

```mermaid
flowchart LR
    locations(locations)
    classDef poly fill:#f9f,stroke:#333,stroke-width:2px
    class locations poly
capitals -.-> locations
cities -.-> locations
countries -.-> cities
countries <==> capitals
highways -.-> locations
highways <==> cities
regions <==> countries
regions <==> cities
tags <==> cities
tags <==> regions
    linkStyle 0 stroke:#3498db,stroke-width:2px
    linkStyle 1 stroke:#3498db,stroke-width:2px
    linkStyle 2 stroke:#2ecc71,stroke-width:3px
    linkStyle 3 stroke:#e67e22,stroke-width:4px
    linkStyle 4 stroke:#3498db,stroke-width:2px
    linkStyle 5 stroke:#e67e22,stroke-width:4px
    linkStyle 6 stroke:#e67e22,stroke-width:4px
    linkStyle 7 stroke:#e67e22,stroke-width:4px
    linkStyle 8 stroke:#e67e22,stroke-width:4px
    linkStyle 9 stroke:#e67e22,stroke-width:4px

    subgraph Legend
        direction TB
        L1(Polymorphic) -.-> L2[Target]
        L3[One-Way] -.-> L4[Target]
        L5[Two-Way] <==> L6[Target]
    end
    class L1 poly
    linkStyle 10 stroke:#3498db,stroke-width:2px
    linkStyle 11 stroke:#2ecc71,stroke-width:3px
    linkStyle 12 stroke:#e67e22,stroke-width:4px

```



## 3. Database

| Table | Required Fields |
| --- | --- |
| capitals | country_id |
| cities | region_id |
| city_highway | city_id, highway_id |
| countries |  |
| highways |  |
| locations | locationable_id, locationable_type |
| regions | country_id |
| singles |  |
| tags |  |


## 4. Relationship Details

<table>
<tr><th>Model</th><th>Method(): Relation</th><th>Related Model</th><th>Reverse Relation</th></tr><tr><td rowspan="2">Capital</td><td><code>country(): belongsTo</code></td><td>Country</td><td>n/a</td></tr>
<tr><td><code>location(): morphOne</code></td><td>Location</td><td>n/a</td></tr>
<tr><td rowspan="5">City</td><td><code>tags(): morphToMany</code></td><td>Tag</td><td>n/a</td></tr>
<tr><td><code>region(): belongsTo</code></td><td>Region</td><td>Region::cities</td></tr>
<tr><td><code>highways(): belongsToMany</code></td><td>Highway</td><td>Highway::cities</td></tr>
<tr><td><code>country(): hasOneThrough</code></td><td>Country</td><td>n/a</td></tr>
<tr><td><code>location(): morphOne</code></td><td>Location</td><td>n/a</td></tr>
<tr><td rowspan="4">Country</td><td><code>capital(): hasOne</code></td><td>Capital</td><td>Capital::country</td></tr>
<tr><td><code>oldest(): hasOne</code></td><td>City</td><td>n/a</td></tr>
<tr><td><code>regions(): hasMany</code></td><td>Region</td><td>Region::country</td></tr>
<tr><td><code>cities(): hasManyThrough</code></td><td>City</td><td>n/a</td></tr>
<tr><td rowspan="2">Highway</td><td><code>cities(): belongsToMany</code></td><td>City</td><td>City::highways</td></tr>
<tr><td><code>locations(): morphMany</code></td><td>Location</td><td>n/a</td></tr>
<tr><td>Location</td><td><code>locationable(): morphTo</code></td><td>Capital, City, Highway</td><td>n/a</td></tr>
<tr><td rowspan="4">Region</td><td><code>tags(): morphToMany</code></td><td>Tag</td><td>n/a</td></tr>
<tr><td><code>country(): belongsTo</code></td><td>Country</td><td>Country::regions</td></tr>
<tr><td><code>cities(): hasMany</code></td><td>City</td><td>City::region</td></tr>
<tr><td><code>capital(): hasOneThrough</code></td><td>Capital</td><td>n/a</td></tr>
<tr><td rowspan="2">Tag</td><td><code>cities(): morphToMany</code></td><td>City</td><td>n/a</td></tr>
<tr><td><code>regions(): morphToMany</code></td><td>Region</td><td>n/a</td></tr>

</table>
