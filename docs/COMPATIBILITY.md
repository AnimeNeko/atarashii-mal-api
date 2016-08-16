Changes from the Ruby MAL API
=============================

*Note:* This document only applies to version 1.0 of the API interface. Later
versions, while similar in spirit, do not guarantee output compatibility.

The external API interface is based on and the output tries to match that
provided by the Unofficial MAL API coded by Chu Yeow. Because of differences
between implementations, some differences exist. They are listed below grouped
by the type of change.

Changes
-------
* The Ruby API encodes text with HTML entities. This API does not perform
  encoding and returns the code as UTF-8 text.
* Only JSON is supported as an output format.
* The verify_credentials endpoint now returns content in the response body. You
  are free to ignore it and use the HTTP response codes, which are not changed.

New Features
------------
* The /friends/ API endpoint has been added.
* The search endpoints support more than 20 results, just use the page parameter
  in the URL similar to anime and manga upcoming.

Bugfixes
--------
* For series with only one date, the Ruby API sets this as the end date. This
  now sets it as the start date.
* For many errors and messages, the output wasn't well-formed. We attempt, to
  the extent it is possible, to always return a well-formed message.
