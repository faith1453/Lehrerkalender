# OSP Projekt Lehrerkalender

Dieses Projekt implemntiert die Anfordernungen des OSP Projekts "Lehrerkalender" der GSO.

## Getting Started

Um das Projekt herunterladen zu können, einfach
```bash
git clone https://github.com/faith1453/Lehrerkalender.git
```
### Vorraussetzungen

Um diese Anwendung produktiv zu verwenden, wird ein Server benötigt, der docker und docker-compose bereitstellt.

Für die Entwicklung wird ein Rechner benötigt, der auch docker und docker-compose bereitstellt. Außerdem werden composer und eine IDE benötigt.

| Server | Rechner |
| ------ | ------- |
| docker | docker |
| docker-compose | docker-compose |
|        | composer |
|        | IDE |
|        | git |

### Installation Rechner

Um die Entwicklung beginnen zu können, muss zunächst das repo wie unter [Getting Started](## Getting Started) herunter geladen werden.

Danach kann die docker Umgebung mit
```bash
docker-compose up -d nginx mysql phpmyadmin redis workspace
```
gestartet werden.

Hiernach findet man unter
```
http://localhost:80
```
die Anwendung zur Ansicht.

Zur Entwicklung müssen die Dateien in einer IDE angepasst werden und die entsprechende Seite neu geladen werden.

### Installation Server

Für die Verbindung zum AWS-Server muss sich zu diesem mit ssh und einem Schlüssel verbunden werden, der Schlüssel liegt unter AWS-KEY. Vom Stammverzechniss des Projektes kann somit mit
```bash
ssh -i "AWS-KEY/aws-osp2.pem" ec2-user@ec2-18-196-136-105.eu-central-1.compute.amazonaws.com
```
eine Verbindung zum Server aufgebaut werden.

## Running the tests

Explain how to run the automated tests for this system

### Break down into end to end tests

Explain what these tests test and why

```
Give an example
```

### And coding style tests

Explain what these tests test and why

```
Give an example
```

## Deployment

Add additional notes about how to deploy this on a live system

## Built With

* [Dropwizard](http://www.dropwizard.io/1.0.2/docs/) - The web framework used
* [Maven](https://maven.apache.org/) - Dependency Management
* [ROME](https://rometools.github.io/rome/) - Used to generate RSS Feeds

## Contributing

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags).

## Authors

* **Billie Thompson** - *Initial work* - [PurpleBooth](https://github.com/PurpleBooth)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc
