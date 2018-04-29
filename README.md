# Requirements
Requires Composer ([Getting Started](https://getcomposer.org/doc/00-intro.md))
You will need **PHP >= 5.6**.
# Convert Excel file to Po file and reverse
Use the command:
```
$ php convert-file excel-to-po <path-to-excel> [<path-to-po>]
```
or
```
$ php convert-file po-to-excel <path-to-po> [<path-to-excel>]
```
# Default behavior
When no path is given for the file to convert to, the default will be used.
The default saves files under the files directory with subdirectory excel for Excel files,
and subdirectory po for Po files. The same filename will be used from the file to convert from.

# Installation:
use Composer to install the application
```
$ composer install
```
