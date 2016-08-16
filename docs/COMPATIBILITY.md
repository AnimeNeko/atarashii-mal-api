Changes from the Ruby MAL API
=============================

The external API interface is based on and the output tries to match that
provided by the Unofficial MAL API coded by Chu Yeow. Because of differences
between implementations, some differences exist. They are listed below grouped
by the type of change.

Please note that this only applies to the "API 1.0" interface. Future revisions,
while based on the format of the Unofficial MAL API, do not offer output
compatibility.

Changes
-------
* The Ruby API encodes text with HTML entities. This code doesn't encode extended
  characters and instead returns non-encoded UTF-8 text.
* The API only outputs JSON. (XML output may be added at a later time.)
* The verify_credentials endpoint now returns content in the response body. You
  are free to ignore it and use the HTTP response codes, which are not changed.

New Features
------------
* The /friends/ API endpoint has been added.
* The search endpoints support more than 20 results, just use the page parameter
  in the URL like in anime and manga upcoming.

Bugfixes
--------
* For series with one date, the Ruby API sets this as the end date. This now
  sets it as the start date.
* For many errors and messages, the output wasn't well-formed. We attempt, to
  the extent it is possible, to always return a well-formed message.
