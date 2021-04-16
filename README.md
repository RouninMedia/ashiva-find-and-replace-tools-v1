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
Although the app resides in a single document, it does not conform to the conventional definition of a **Single Page App (SPA)**.

A conventional **SPA** would *not* need to be hosted on a remote server and / or be able to run server-side scripts in `PHP`.

Hence the alternative label: *Single File App*.

## Anything else?

Yes. A major innovative feature of the **MultiPage Editor** is that the `URL queryString` *always* describes the **entire current state** of the app.

This means that:

 - the `URL` can be bookmarked at any point and when the bookmark is clicked later, the former state of the app will be precisely reproduced
 - the browser's reload button may be pressed at any point without any visible change to the view the app is currently displaying
