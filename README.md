This code represents but one of many ways to render teletext! 

Whilst I've tried my best to stick to the spec where I can, it's not perfect. But you're welcome to make use of it, especially if you're in a similar situation to me, where you just want a way to directly display teletext TTI files on a website without any special server shenanigans, but in a way that looks/feels like real teletext. When I created this, my website was on shared hosting, no databases, but I did have PHP.

# Installation

- Copy the folder */teletext* to the top level of your hosting.
- Copy any TTI files from any service you'd like to host into subfolders in */teletext/services* (e.g. in my case, I have */teletext/services/nemetext*)
- Access your service at */teletext/viewer/?service=\<service subfolder name\>*
- Alternatively, embed your service into your page of choice by including */teletext/viewer/embed.php*

# Operation
The teletext service can be browsed using the keyboard or the on-screen remote.
- R, G, Y, B: Fastext red, green, yellow, and blue respectively
- ? or /: reveal
- H: hold
- I: index
- S: size (resize)

# Optional parameters

Add the following parameters to the query string:
- *fullscreen*: displays the page fullscreen (or full height of your div/iframe) with no remote control
- *minitv*: renders the teletext in a pretend TV

# Optional audio

If you would like to add background music, create a folder at */teletext/music* and add MP3 files (that you own the rights to). A great source of royalty-free music is Pixabay (https://pixabay.com/music/).
