<!--
written with: php artisan run:test-project
by Console Command Workbench\App\Console\Commands\RunTestProjectManagerCommand
do not manually edit this file as it will be overwritten

-->

## Model relations

<table>
<tr><th>model</th><th>direct</th><th>indirect</th></tr><tr><td>Capital</td><td>Country</td><td>Location</td></tr>
<tr><td>Location</td><td>Capital, City, Highway</td><td></td></tr>
<tr><td>City</td><td>Region, Tag</td><td>Country, Highway, Location</td></tr>
<tr><td>Highway</td><td></td><td>City, Location</td></tr>
<tr><td>Country</td><td>Capital, Region</td><td>City, City</td></tr>
<tr><td>Tag</td><td></td><td>City, Region</td></tr>
<tr><td>Region</td><td>City, Country, Tag</td><td>Capital</td></tr>
<tr><td>Single</td><td></td><td></td></tr>
<tr><td>Green (not defined)</td><td></td><td></td></tr>

</table>


## Table relations

```mermaid
flowchart TD
locations --> capitals
locations --> cities
locations --> highways
capitals ==> countries
cities ==> regions
highways <==> cities
regions ==> countries

```



## Database

<table>
<tr><th>table</th><th>required fields</th></tr><tr><td>capitals</td><td>country_id</td></tr>
<tr><td>cities</td><td>region_id</td></tr>
<tr><td>city_highway</td><td>city_id, highway_id</td></tr>
<tr><td>countries</td><td></td></tr>
<tr><td>highways</td><td></td></tr>
<tr><td>locations</td><td>locationable_id, locationable_type</td></tr>
<tr><td>regions</td><td>country_id</td></tr>
<tr><td>singles</td><td></td></tr>
<tr><td>tags</td><td></td></tr>

</table>

