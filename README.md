# Anime tracker



## App description
It's a trucker where you can make your own anime list to track which anime you are watching or which you abandoned! Also you can see stats on your watching anime!
## Features

- **Anime list search:** You can easily search your anime list in two ways, by title and status of the anime you are interested in.
- **Anime stats:** If you like statistics and are a fan of charts, you will find something for yourself there. You can observe what type of anime you watch most often and in what category.
- **Easy adding anime:** You can easily add all your favorite anime to the list. The only two things you need to enter are the title and the number of episodes, the rest you can easily choose from the drop-down list.
- **Delete anime from list:** You can also easily delete your added anime with one button when, for example, you make a mistake or when you are not interested in the anime.
## Let's start!

### 1. Clone Repository
To clone repository you can use the command below:
```shell
git clone https://github.com/Pabik14/Project_WdPAI_AT.git
```

### 2. Run Docker Image
You need to have [Docker](https://www.docker.com/) installed on your environment  
Move to the project's directory and run the command below:

```shell
docker-compose up --build
```
### 3. Import database

To import database you need to go to pgAdmin website under this URL:
```shell
http://localhost:5050/
```
Then you need to log in using this login and password
<details>
  <summary>Login data</summary>

```shell
admin@example.com
admin
```
</details>

<br>
Next step is to connect your database using these login details
<br>
<br>
<details>
  <summary>Login data</summary>

```shell
docker
docker
```
</details>
<br>

The last step is to enter the ready commands to create the table, copy and paste them into the query tool and run which are available at this [link](https://github.com/Pabik14/Project_WdPAI_AT/blob/master/public/backup/backup.sql).

### 4. Logging in:

There are 3 predefinied accounts already included when you import database.

#### Admin Role:
<details>
  <summary>Admin</summary>

```shell
Login: admin@admin.pl
Password: 123
```

</details>

#### User Role:
<details>
  <summary>Test</summary>

```shell
Login: test@test.pl
Password: 123
```

</details>

<details>
  <summary>Test2</summary>

```shell
Login: test2@test.pl
Password: he123avy
```

</details>

## Database

### Diagram ERD 
![ERD Diagram](https://i.imgur.com/vgNfAQj.png)




## Technologies used
- Git
- Github
- Docker
- HTML
- CSS
- JavaScript
- PHP
- PostgreSQL

