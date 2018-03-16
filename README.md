# PHP script for archiving logs over HTTP

This script was mainly created for archiving X-road v6 security server logs but also work with v5 security server.
To use it you have to setup either nginx or apache.

The script will create hierarchical folder structure where the logs are archived.
It will create folder structure with hostname, year and month.

The script elso has more error handling than the default script provided in X-road repository (https://github.com/ria-ee/X-Road/blob/develop/src/addons/messagelog/scripts/demo-upload.pl).

Following checks are done:
* MIME check (gzip or zip)
* Archiving directory check (writable or not)
* some other checks regarding PHP upload configuration.

If any of those checks fail the script will return HTTP error code. This ensures that Security server will try to upload the archived logs again without deleting any when uploading failed.
