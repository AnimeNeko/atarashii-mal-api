Changes from the Ruby MAL API
=============================

The external API interface is based on that provided by the Unofficial MAL API
coded by Chu Yeow, but changes have been made. They are listed below grouped by
the type of change.

Changes
-------
* The Ruby API encodes text with HTML entities. This code uses non-encoded UTF-8.
* The API only outputs JSON. (XML output will be added at a later time.)

New Features
------------
* The /friends/ API endpoint has been added.

Bugfixes
--------
* For series with one date, the Ruby API sets this as the end date. This now
  sets it as the start date.
* For many errors and messages, the output wasn't well-formed. We attempt, to
  the extent it is possible, to always return a well-formed message.
