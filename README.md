# Help2022 - powered by Drupal 9 / Docksal

This is a quick-and-dirty implementation based on Drupal 9 - full running version see [here - https://help2022.com](https://help2022.com/).

Features:

- ...TBD

## Setup instructions

### Step #1: Docksal environment setup

**This is a one time setup - skip this if you already have a working Docksal environment.**

Follow [Docksal environment setup instructions](https://docs.docksal.io/getting-started/setup/)

### Step #2: Project setup

1. Clone this repo into your Projects directory

    ```
    git clone https://github.com/Nikro/help2022.git help2022
    cd help2022
    ```

2. Initialize the site

    This will initialize local settings and install the site via drush

    ```
    fin init
    ```

3. Point your browser to

    ```
    http://help2022.docksal
    ```

When the automated install is complete the command line output will display the admin username and password.
