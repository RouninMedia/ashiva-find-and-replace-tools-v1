# Ashiva MultiPage Editor
The **Ashiva MultiPage Editor** is a standalone *Single File App* which finds and replaces text across *multiple web pages* on any site using an **Ashiva WebRig**.

## What technologies does the MultiPage Editor use?
The *Single File App* includes the following technologies:

 - `PHP`
 - `HTML5`
 - `CSS3`
 - `Javascript ES2015+`
 - `JSON`
 - `SVG` (in the favicon and in two button CSS `background-images`)

## What's with the term *Single File App*?
Although the app resides in a single document, it does not conform to most conventional definitions of a **Single Page App (SPA)**.

 - it doesn't require only a single page load in a web browser (in fact the app repeatedly reloads itself)
 - it doesn't rely solely on client-side rendering (CSR) - in fact, arguably **all** of the rendering is server side
 - the app depends on `PHP` and, consequently, it must be hosted on a remote server - it cannot be run locally at all (and certainly not *offline*)

However, the major advantage of this single-document, remote-hosted app is that it can run both client-side scripts in `JS` ***and*** server-side scripts in `PHP`.

Hence the alternative label: *Single File App*.

## Anything else?

Yes. A major innovative feature of the **MultiPage Editor** is that the `URL queryString` *always* describes the **entire current state** of the app.

This means that:

 - the `URL` can be bookmarked at any point and when the bookmark is clicked later, the former state of the app will be precisely reproduced
 - the browser's reload button may be pressed at any point without any visible change to the view the app is currently displaying
