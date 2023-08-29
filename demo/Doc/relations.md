<!--
written with: php artisan composer write-file
by Console Command SchenkeIo\LaravelRelationManager\Demo\DemoCommand
do not manually edit this file as it will be overwritten

-->
```mermaid
flowchart LR
capitals ---> countries
regions ---> countries
cities ---> regions
capitals-- through ---regions
city_high_way ---> cities
city_high_way ---> high_ways


```
