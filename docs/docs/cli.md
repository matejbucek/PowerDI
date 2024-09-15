# PowerDI CLI

We now have our own CLI tool, which will help you to create a new project and generate new components, services etc. in
the near future.

Note that the CLI commands might change in the future!

## Installation

To install the CLI tool, visit the [repository](https://github.com/matejbucek/PowerDI-CLI) and follow the instructions.

## Usage

### Create a new project

To create a new project, run the following command:

```bash
powerdi new my-project
```

This will create a new project in the `my-project` directory.

### Run the development server

To run the development server, navigate to the project directory and run the following command:

```bash
powerdi serve
```

### Build the project

To build the project, navigate to the project directory and run the following command:

```bash
powerdi build
```

This will create a `build` directory with the archived project.

### Configuration

The CLI tool uses a configuration file, which is located in the project directory. The configuration file is named
`powerdi.yaml`.

```bash
powerdi config
```

### Help

To get help, run the following command:

```bash
powerdi help
```