# Worldbuilders Wiki

This is a personal project for creating a lightning-fast and user-friendly wiki, aimed towards world-building.

This project is still under development. The core functionality is complete and functioning, and I use it myself, however some functionality is yet to be developed, listed below.

## Quick Links

 - [The frontend's code.](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/web/frontend)
 - [The backend's code.](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/web/backend)
 - [The public directory.](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/web/public)
 - [The general wikitext grammar.](https://github.com/AlexSWall/worldbuilders-wiki/blob/main/web/backend/app/WikitextConversion/Grammar.pegphp)
 - [The infobox wikitext grammar.](https://github.com/AlexSWall/worldbuilders-wiki/blob/main/web/backend/app/Infoboxes/Grammar.pegphp)
 - [The (backend's) configuration directory.](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/config)

## Table of contents

<!--ts-->
 - [Motivation](#motivation)
 - [Project Goal](#project-goal)
 - [Key Features](#key-features)
   - [Characters and Wikipage Permissions](#characters-and-wikipage-permissions)
   - [Planned/In-Development Features](#plannedin-development-features)
 - [Usage](#usage)
   - [Makefile Usage](#makefile-usage)
   - [Database Instance Data](#database-instance-data)
 - [Technology Stack and Design](#technology-stack-and-design)
   - [Backend](#backend)
   - [Wikitext](#wikitext)
   - [Frontend](#frontend)
     - [Frontend Design Choices](#frontend-design-choices)
 - [Security](#security)
   - [Authentication Flow](#authentication-flow)
     - [Authentication Design Choices](#authentication-design-choices)
   - [XSS Prevention](#xss-prevention)
   - [CSRF Protection](#csrf-protection)
<!--te-->

## Motivation

World-building is the act of creating a fictional/fantasy world, typically as a setting for a novel, game, or otherwise.

In doing world-building, the problem naturally arises of how to store information and notes.

Typically, one goes with one of the following options:
 1. Hand-write the information and notes, and store it in books or folders.
 1. Write a collection of text files (or Word documents, etc.) on a computer, either locally or online.
 1. Use a wiki to store the information.
 1. Use another application to store information, not tailored to a wiki-style system.

For the task of worldbuilding, often the benefits of being searchable, remotely-accessible, and having text that can link to other notes or text makes using a wiki the most powerful theoretical choice.

However, the current offering of wiki software typically has one or more of the following problems for casual worldbuilders:
 - The markup language is complicated and hard to read, presenting a high barrier to entry for use, a large amount of background complexity, and a difficulty in making fast edits.
 - The wiki has irremovable visual functionality and styling, including ads, links to other wikis, and various Javascript tracking, that is actively unhelpful and undesirable.
 - Aesthetically 'old school' styling and bare functionality.
 - The slow loading of pages, both due to backend and frontend inefficiencies.
 - The particularly slow loading of the underlying wikitext for when a page is to be edited, often presented via a slow-to-load WYSIWYG editor.
 - Little to no advanced functionality for quickly navigating between pages.

## Project Goal

As such, this project's unique selling points and core aims are to create a fast, flexible, and easy wiki with all of the motivating problems addressed, and with additional features key for some uses of a worldbuilding wiki—see below.

In addition to this goal, this project also presented the following opportunities and goals for me personally:
 - Learn a wide variety of new programming languages, technologies, and frameworks (detailed below).
 - Implement a secure authentication/account system.
 - Create a product that I and others can use to store and access information quickly, easily, and in a wiki-style.

## Key Features

This wiki provides a range of features:

 - **Fast initial page access.**
 - **Instant loading of previously-visited pages via caching.**
   - The cache is kept consistent with the wiki on each load asynchronously.
 - **A clean, easy-to-use UI.**
 - **A custom, lightweight, easy-to-use wikitext language.**
   - This includes wiki 'infobox' support, including the creation and modification of infobox templates and their use in wikipages.
   - Anti-XSS checks are in place to ensure the result of the wikitext's compilation is safe.
 - **User accounts**, requiring an email for sign-up for account recovery.
 - **Character accounts with rule-based wikipage section permissions**—see below.

### Characters and Wikipage Permissions

One novel feature that this wiki offers, for those that would like to use it, is the concept of a 'character view' of the wiki, as well as non-public sections.

An account can have multiple 'characters', and each of these has certain attributes/groups—for example, for which area of the world they are located in.

When a wikipage section is written, one can optionally add rules inline for the attributes required to view the section.

This way, the writer can tailor the content shown depending on the perspective of the 'character'.

Furthermore, the writer can set some sections to be public or private.

A key motivation for this would be for the Game Master of a table-top RPG (such as Dungeons & Dragons) sharing their Worldbuilder's Wiki with their players.  
When doing this, some notes may only be appropriate for particular players, playing particular characters, to see, while other notes may be private worldbuilding notes for the Game Master's eyes only.

Another use for this is to simply hide some sections under construction from public view until complete.

More complex rules can be written using boolean expressions.  
For example, a heading on a page for History, which is only readable by elven and dwarven wizards, could look like
```
== History | ( is_elf or is_dwarf ) and is_wizard ==

This information is only viewable to elven and dwarven wizards...
```

### Planned/In-Development Features

 - Additional navigation features (in progress, partially complete).
 - Search functionality.
 - Character account management.

## Usage

The wiki, including the background services, can all be spun up using `docker-compose up`.

However, doing so will need some configuration for each instance.  
The files to be added are the two backend configuration files (for development and testing) and the `.env` environment variables file for NGINX and SQL—`.example` files can be found for each of these.

This is handled in a way such that one can easily store the instance files in another directory, hard-link to these from within this repository, and then back this configuration data up.  

### Makefile Usage

The Makefile is provided to help easily manage the wiki instance.

It provides the following convenience functionality:

 - `make setup`               Set up the repository for a wiki instance from instance data contained in a root repo directory named 'setup'.
 - `make start`               Starts the docker-compose containers as a daemon. (Just aliases `docker-compose up -d`.)
 - `make stop`                Stops the running docker-compose daemon. (Just aliases `docker-compose down`.)
 - `make db-dump`             Create a backup of all databases.
 - `make db-restore`          Restore all databases from a backup.
 - `make test`                Run the backend tests.
 - `make clean`               Delete files and directories generated when running the wiki, such as logs and libraries.
 - `make distclean`           Run clean and additionally delete the instance's configuration files.

### Database Instance Data

The wiki instance's data, including the wikitext and accounts, are stored in a MySQL database, and can be easily backed up and restored from using the above commands.

This gives a convenient and strong guarantee on preventing significant data loss, and quick turn-around on spinning up a new instance.

## Technology Stack and Design

This project is split into the frontend and backend.

### Backend

The backend uses docker-compose to manage the containers.

The containers are:
 - An Nginx webserver entry-point, which uses fastcgi to communicate with a PHP container.
 - A PHP 8 webserver backend with Slim at the core.
 - A Composer container for managing the PHP dependencies.
 - A MySQL webserver containing the website instance data.
 - Two convenience containers for development:
   - One for watching and automatically recompiling the TypeScript/React code, the SCSS code, and the Wikipeg grammars.
   - One for PHP MyAdmin database access.

The backend handles authentication, including automated authentication emails (on sign-up, account recovery, etc.), and also Wikitext parsing.

The code for the backend PHP code can be found in [the backend directory](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/web/backend).

Otherwise, the backend configuration primarily happens in [the configuration](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/config) directory.

### Wikitext

The wikitext of the service is a custom syntax, deliberately sharing similarities to existing wikitexts (such as Wikipedia's) but with a significantly streamlined feature-set for ease of use.  
For example, many features and design goals (such as perfectly-bijective wikitext-HTML conversation) simply are not required and significantly complicate the grammar.

This has been implemented by writing a full wikitext grammar, and compiling this using [Wikimedia's wikipeg](https://github.com/wikimedia/wikipeg), with automated compilation handled by the dev Docker container.

You can find these grammars at the following locations:
 - [General Wikitext Grammar](https://github.com/AlexSWall/worldbuilders-wiki/blob/main/web/backend/app/WikitextConversion/Grammar.pegphp).
 - [Infobox Wikitext Grammar](https://github.com/AlexSWall/worldbuilders-wiki/blob/main/web/backend/app/Infoboxes/Grammar.pegphp)

### Frontend

The frontend uses React and TypeScipt, transpiled with polyfills to widely-compatible JavaScript using Webpack and Babel.

Additionally, it uses SCSS, which is transpiled to CSS.

The majority of the code for the frontend can be found in [the frontend directory](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/web/frontend), and the hard-coded public source can be found in [the public directory](https://github.com/AlexSWall/worldbuilders-wiki/tree/main/web/public).

The automatic transpilation of both the TypeScript and SCSS, while being developed, is handled by the dev Docker container.

#### Frontend Design Choices

The webpage is designed in a Single Page Application (SPA) design, but without many key issues that can come with such designs.

In particular, all wikipages are shows under the same URL/app, but with a different hash.  
For example, one wikipage may be stored at `https://my.url.com/#my-page`.  
As such, clicking a link in the wiki will simply change the hash.

We can then use a JavaScript event listener on the `hashchange` event to fire an asynchronous API request to update the wikipage contents (only) behind the scenes.
When loading a cached page, an API request is still made for the page to the server in case it has been changed since the last access, and this is swapped in when retrieved.

This provides the following key benefits above standard website links (such as in Wikipedia):
 - No full-page reloading is required, as the pre-hash URL has not changed, saving one from making a variety of requests to check for updates to the same resources, and preventing any page-load visual changes.
 - We can easily store the data for recently-visited pages, or predictively pre-load pages to switch to, to make switching pages *instant*.
   - For example, we could switch on a cache-heavy mode to pre-load future pages based on URLs contained in the current page, as well as utilizing the UI's navigation methods.

Ensuring the scrolling location of the page is also maintained when navigating back and forwards through the tab's history required the use of a `popstate` event listener.

### Security

All random data generation is generated using a secure random number generator.

#### Authentication Flow

User accounts require an email address for sign-up.

To sign up, a user provides a username, password, and email address.

The frontend then sends the username, email, and a SHA1 hash of the password, with a hard-coded salt added, to the webserver.

The webserver hashes the SHA1 password hash with bcrypt (with this choice and also the cost both being configurable), and this final bcrypt hash is saved in the database alongside the username and email.

Then, a confirmation email is sent to the email provided for the user, with an activation link containing a secure randomly-generated identifier parameter, that the user must click to activate the account.

Once the link has been clicked and the email and identifier have been confirmed to match and be in the database, the account is activated and login can occur.

On an attempt to log in, the webserver checks that the provided password, when double-hashed in the same way to before, gives the stored password. This check is performed in a timing-attack-safe manner.

On success, the user is logged in, and their session is managed via the standard PHP session management (using the PHPSESSID cookie).

##### Authentication Design Choices

Strictly speaking, taking the SHA1 hash of the password with a hard-coded salt in the frontend is not needed, and SHA1 is not a good choice for a password hash, particularly without a variable salt, due to being comparatively fast to brute-force.  
Accordingly, bcrypt is used, and in the backend, as is standard for storing passwords.

However, while the frontend SHA1 hash does not provide any protection against authentication if the hash is obtained, as it can be used in the place of the password for authentication, it does provide obfuscation of the true passwords.  
This is good for a few reasons:
 - If the password is used on other websites, or similar to other passwords, but is otherwise very hard to brute-force (even for SHA1), this provides protection to those other services.
 - If the password should somehow accidentally be logged or reported somewhere (e.g. via an exception), the true password is not displayed raw to the administrator.

Furthermore, an additional SHA1 cannot *hinder* the security, as long as there is sufficient understanding of what it is not providing.

A fixed salt is prepended to the password before being hashed to prevent trivial SHA1 rainbow table lookups.

#### XSS Prevention

Wikitext is compiled to HTML to produce the wikipage content, formatted with text effects such as bold and italics, organization such as bullet points, a table of contents automatically generated, links, and more.

Therefore, care must be taken to ensure that user-provided wikitext cannot be compiled to generate malicious HTML content, which otherwise would enable Cross-Site Scripting attacks.

When provided, the wikitext provided is parsed using the [wikitext grammar parser](https://github.com/AlexSWall/worldbuilders-wiki/blob/main/web/backend/app/WikitextConversion/Grammar.pegphp).

To reduce the attack surface of XSS vulnerabilities, many wikitext components must be alphanumeric, and those which otherwise directly copy flexible user input to the resulting HTML have HTML special characters either removed or replaced, depending on which is most suitable for each occurrence.

#### CSRF Protection

Forms must be protected from Cross-Site Request Forgery attacks.

To do this, CSRF tokens are included for every form with a POST request, and these are checked by automatic middleware in the backend.

These CSRF tokens are placed into the original page HTML via a CSRF token template in the base HTML pages, which is parsed out by the frontend and stored.
