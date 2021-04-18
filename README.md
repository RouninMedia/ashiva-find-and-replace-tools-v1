# Ashiva Find and Replace Tools v1

**Ashiva Find and Replace Tools v1** is a suite of standalone **Single Page App (SPA)** tools used to update any site using an **Ashiva WebRig**.

 - the **Ashiva MultiPage Editor** is an **SPA** which finds and replaces text across *multiple Pages*
 - the **Ashiva Scaffold Editor** is an **SPA** which finds and replaces text across *multiple Scaffolds*
 - the **Ashiva PageManifest Editor** is an **SPA** which creates, reads, updates and deletes entries on any *PageManifest*
 - the **Ashiva SiteManifest Editor** is an **SPA** which creates, reads, updates and deletes entries on any *SiteManifest*

## What SPA Framework do the Ashiva Find and Replace Tools v1 use?

**Ashiva Find and Replace Tools v1** are **SPAs** built using the **Reflective SPA** (or **veSPA**) model of *SPA Architecture*.




## What's with the term *Single File App*?
Taking any one of the four apps, although the app resides in a single document, it does not conform to most conventional definitions of a **Single Page App (SPA)**.

 - it doesn't require only a single page load in a web browser (in fact the app repeatedly reloads itself)
 - it doesn't rely solely on client-side rendering (CSR) - in fact, arguably **all** of the rendering is server side
 - the app depends on `PHP` and, consequently, it must be hosted on a remote server - it cannot be run locally at all (and certainly not *offline*)

Hence the alternative label: *Single File App*.

The ***major advantage*** of this single-document, remote-hosted app is that it can run not only:

 - client-side scripts in `JS`

***but also:***

 - server-side scripts in `PHP`

## Anything else?

Yes. A major innovative feature of the **Ashiva Find and Replace Tools v1** is that the `URL queryString` *always* describes the **entire current state** of each app.

This means that:

 - the `URL` can be bookmarked at any point and when the bookmark is clicked later, the former state of the app will be precisely reproduced
 - the browser's reload button may be pressed at any point without any visible change to the view the app is currently displaying


## What technologies do the Ashiva Find and Replace Tools v1 use?
Each of the four **veSPA** *Single Page Apps* includes the following technologies:

 - `PHP`
 - `HTML5`
 - `CSS3`
 - `Javascript ES2015+`
 - `JSON`
 - `SVG` (in the favicon and in CSS `background-images`)
