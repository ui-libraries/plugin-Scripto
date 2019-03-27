Scripto (plugin for Omeka)
==========================


Summary
-------

[Scripto] is an open-source tool developed by [Center for History & New Media]
that allows collaborative transcription of digital files. Built on [MediaWiki],
Scripto is intended to be used as an extension of a content management system.
Scripto is currently available as a plugin for [Omeka], Wordpress, and Drupal.

[DIYHistory|transcribe] uses Scripto with Omeka. Visit [DIYhistory|code] for an
overview of the technology stack and implementation information.

Features of the Scripto plugin for Omeka can be found at [Github|Scripto]. The
UI Libraries fork of plugin-Scripto adds the following features:

* Track completion status of document pages (i.e., ‘Not Started’, ‘Needs
Review’, ‘Completed’).
* Track completion progress of documents based on page statuses.
* Sort documents within their collection by most completed, floating least
completed to the top.
* Initialize document page text entry box with pre-existing text, if available
(helpful if using Scripto to correct OCR for typescript pages).
* Fill document pages with a pre-existing text, if available in a field element
of each file, via a button in the admin item pages (helpful to re-initialize a
list of document pages at any time).
* The source element used to initialize or to reinitialize a document page can
be chosen (helpful to keep track of original source). Scripto:Transcription is
used by default. It should be a file level element.
* On every page action, automatically import transcriptions from MediaWiki as
file metadata.
* Possibility to define items that should not be transcribed via the admin page
items/show.
* Choice of the order of  the pages inside a document by predefined order,
original filename or file id.
* Choice of the derivative image to display (default is the original one, but
fullsize or any other derivative can be used).

The UI Libraries has also created an Omeka theme, [Scribe], to make use of these
new features.

To accommodate these features, UI Libraries has added four elements to the
Scripto metadata element set: “Status”, “Percent Needs Review”, “Percent
Completed” and “Weight”. These values are calculated and updated with every
“Save”, “Approve”, or “Unapprove” action made on a document page.

“Status” stores the completion status of a document page (i.e., “Not Started”,
“Needs Review”, “Completed”) in the page-level metadata. Scribe displays this
value on each page thumbnail in the items/show page.

In the document (item) level metadata, “Status” stores the status of a document.
Currently, this is used only for the status “Not to transcribe”. Values for
“Percent Needs Review” and “Percent Completed” represent the percentage of pages
within the document carrying the “Needs Review” and “Completed” statuses,
respectively. These percentages are used by Scribe to display the stacked
progress bars for each item in the collections/show page. The 6-digit sorting
number stored in “Weight” is used by Scribe to sort items on collections/show
page by completion progress, sinking the most completed items to the bottom and
floating the least completed to the top.

[NLW] added the ability to create mediawiki account through Scripto API. Code
heavily borrowed from [Joris Lambrechts] particularly [commit5].

Installation
------------

Install [MediaWiki].

Uncompress files and rename plugin folder "Scripto".

Then install it like any other Omeka plugin and follow the config instructions.

The new fields for the Scripto element set will automatically be installed with
a fresh install of the UI Libraries fork of plugin-Scripto or with an upgrade
of the upstream version.


Metadata
---------

Metadata and content files are uploaded to Omeka using the fork of the plugin
[CsvImport]. CSV Import is an Omeka plugin for batch uploading metadata and
content file to Omeka. The UI Libraries fork of this plugin allows for the batch
uploading of page-level (file) descriptive metadata. To upload using the
UI Libraries’ version, first upload the csv file of items as instructed in the
CsvImport documentation (choosing ‘Record Type: Item’ in the Csv Import
interface). After uploading and creating the items, upload the csv file of
page-level metadata (choosing ‘Record Type: File’ in the CsvImport interface).
Map the Original filename to ‘Filename?’ in Step 2 of the CSV Import interface.
See _sample-data _Item.csv_ and _sample-data _File.csv_ for an example. If you
are pulling content from your version 6 CONTENTdm installation, you may find
this [sample] gist helpful in generating csv upload files.

**Document-level (item) metadata**

<table border="1"><tbody>
<tr><td><p><strong>Element</strong></p></td><td><p><strong>Omeka element</strong></p></td><td><p><strong>Comments</strong></p></td></tr>
<tr><td><p>Title</p></td><td><p>dc:Title</p></td><td><p>The title of the document</p></td></tr><tr><td><p>Source URL</p></td><td><p>dc:Source</p></td><td><p>(optional) The URL for the original location or master record for the document (If you are replicating the document from another digital environment).</p></td></tr>
<tr><td><p>Source identifier</p></td><td><p>dc:Identifier</p></td><td><p>(optional) An identifier for the document that ties it to the original location or master record (If you are replicating the document from another digital environment). Note that Omeka will generate a system identifier for each document (item).</p></td></tr>
<tr><td><p>Digital collection URL</p></td><td><p>dc:Is Part Of</p></td><td><p>(optional) The URL for the digital collection that the document belongs to in its original digital environment (If you are replicating the document from another digital environment).</p></td></tr>
<tr><td><p>Finding aid URL</p></td><td><p>dc:Relation</p></td><td><p>(optional) The URL for the finding aid of the document&rsquo;s source collection. </p></td></tr>
<tr><td><p>Sorting number</p></td><td><p>Scripto:Weight</p></td><td><p>6-digit number for sorting the item within its collection in the collections/show display. Set to &lsquo;000000&rsquo; as default. This will get updated every time a document page from the document is saved, approved, or unapproved.</p></td></tr>
<tr><td><p>Percent needs review</p></td><td><p>Scripto:Percent Needs Review</p></td><td><p>Percentage of pages with status &lsquo;Needs Review&rsquo;. No default needed. This will get updated every time a document page from the document is saved, approved, or unapproved.</p></td></tr>
<tr><td><p>Percent completed</p></td><td><p>Scripto:Percent Completed</p></td><td><p>Percentage of pages with status &lsquo;Completed&rsquo;. This will get updated every time a document page from the document is saved, approved, or unapproved.</p></td></tr>
</tbody></table>

**Page-level (file) metadata**

<table border="1"><tbody>
<tr><td><p><strong>Element</strong></p></td><td><p><strong>Omeka element</strong></p></td><td><p><strong>Comments</strong></p></td></tr>
<tr><td><p>Original filename</p></td><td><p></p></td><td><p>The file location specified in the csv item upload file. The UI Libraries fork of plugin-CsvImport uses this filename to find the Omeka file record for applying the page-level metadata.</p></td></tr>
<tr><td><p>Page label</p></td><td><p>dc:Title</p></td><td><p>The label for the page</p></td></tr>
<tr><td><p>Page-level source URL</p></td><td><p>dc:Source</p></td><td><p>(optional) The URL for the original location or master record for the document page (If you are replicating the document from another digital environment).</p></td></tr>
<tr><td><p>Source identifier</p></td><td><p>dc:identifier</p></td><td><p>(optional) An identifier for the document page that ties it to the original location or master record (If you are replicating the document from another digital environment). Note that Omeka will generate a system identifier for each document page (file).</p></td></tr>
<tr><td><p>Transcription</p></td><td><p>Scripto:Transcription</p></td><td><p>The transcription for the document page. This will get updated every time a document page is saved or approved. You may pre-populate this field on ingest to Omeka with OCR or existing transcription, if desired.</p></td></tr>
<tr><td><p>Status</p></td><td><p>Scripto:Status</p></td><td><p>Completion status of the document page (&lsquo;Not Started&rsquo;, &lsquo;Needs Review&rsquo;, &lsquo;Completed&rsquo;). Set to &lsquo;Not Started&rsquo; as default. This will get updated every time a document page is saved, approved, or unapproved.</p></td></tr>
<tr><td><p>Omeka file order</p></td><td><p>no map</p></td><td><p>The order of the page within the document. When used with the ui-libraries/plugin-Csv-Import fork, this value will assure files are in the correct sequence.</p></td></tr>
</tbody></table>


Warning
-------

Use it at your own risk.

It's always recommended to backup your files and database so you can roll back
if needed.


Troubleshooting
---------------

See online issues on the [Scripto issues] page on GitHub.


License
-------

This plugin is published under [GNU/GPL].

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


Contact
-------

Current maintainers:

* [Center for History & New Media]

First version of this plugin has been built by [Center for History & New Media].
Next, [UI libraries] has forked it in order to add some features. This fork has
been merged into the main version for Omeka 2 for [École des Mines ParisTech]
and completed.


Copyright
---------

* Copyright Center for History and New Media, 2008-2016
* Copyright Shawn Averkamp, 2012-2013
* Copyright National Library of Wales 2015
* Copyright JorisLambrechts
* Copyright Daniel Berthereau, 2013-2016


[Scripto]: http://scripto.org/
[Center for History & New Media]: http://chnm.gmu.edu/
[MediaWiki]: https://www.mediawiki.org
[Omeka]: https://omeka.org
[DIYHistory|transcribe]: http://diyhistory.lib.uiowa.edu/transcribe/
[DIYHistory|code]: http://diyhistory.lib.uiowa.edu/code.html
[Github|Scripto]: https://github.com/ui-libraries/DIYHistory-transcribe
[Scribe]: https://github.com/ui-libraries/scribe
[CsvImport]: https://github.com/Daniel-KM/Omeka-plugin-CsvImport
[sample]: https://gist.github.com/saverkamp/4732757
[Scripto issues]: https://github.com/Omeka/plugin-Scripto/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html "GNU/GPL v3"
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
[UI Libraries]: http://www.lib.uiowa.edu "University of Iowa Libraries"
[École des Mines ParisTech]: http://bib.mines-paristech.fr
[Joris Lambrechts]: https://github.com/libis/Schatkamer "Joris Lambrechts"
[commit5]: https://github.com/libis/Schatkamer/commit/dec4cebb37dc03f1b16603115c43f9ed593ffedf
[Paul Mccann]: https://github.com/hotnuts21/plugin-Scripto "Paul McCann"
[NLW]: http://www.llgc.org.uk
