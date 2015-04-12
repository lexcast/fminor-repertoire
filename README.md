#Fminor Repertoire
This is the default repertoire of the Fminor project.

####Generators
- **Asset**: generates scripts and css, adds links to assets in the base template and create bower file with library dependencies.
- **Controller**: generates functional controllers classes and actions with their code.
- **Routing**: generates routes in the `routes.php` file.
- **Templating**: generates templates, layouts or little fragments of html needed, with includes and extends.

####Request
Each generator look in an array of requests that are basically one for each kind of generator, plus the library request regarding to the asset generator.

####Chords
You can use this in your `chords.yml` to build your project.

In this repertoire there are:

- **Footer**
- **Header**
- **Menu**
- **Section**
- **Webpage**

You can embed fragment into other or generate links between them.

See more on [lexcast/fminor](https://github.com/lexcast/fminor).
