### Internship Task: Event Ticketing Bot for Telegram

#### Objective
Develop a Telegram bot for event ticketing that allows users to obtain free tickets and adds them to a single Telegram group dedicated to the event.

#### Task Description
Your task is to create a Telegram bot using either PHP, Node.js, or Python that handles the following functionalities:
1. **Event Information:** Provides users with details about the event.
2. **Ticket Request:** Allows users to request free tickets through the bot.
3. **User Registration:** Collects necessary user information during the ticket request.
4. **Group Invitation:** Adds users to a specific Telegram group upon successful ticket request.

#### Requirements
- **Bot Commands:**
  - `/start` - Greets the user and provides event details.
  - `/register` - Initiates the ticket registration process.
  - `/help` - Provides help and usage instructions.

- **Ticket Request Process:**
  - Collect user's name, email, and number of tickets.
  - Confirm the request and send a ticket (can be a simple confirmation message with a unique ID).

- **Group Management:**
  - After successful ticket request, automatically add the user to the designated Telegram group.
  - Ensure the bot has the necessary permissions to add users to the group.

- **Database:**
  - Maintain a simple database (e.g., JSON, SQLite) to store user information and ticket details.

#### Submission Guidelines
- **Fork Repository:** To accept this task, fork the GitHub repository [EventRSVPBot]([https://github.com/CeyLabs-TGBot-EventRSVPBot](https://github.com/CeyLabs/CeyLabs-Intern-TGBot-EventRSVP/)) which contains the hello bot as a starting point.
- **GitHub Repository:** Ensure your forked repository contains:
  - Source code for the Telegram bot.
  - A README file with instructions on how to set up and run the bot.
  - Any necessary configuration files (e.g., `config.json` for bot tokens and group IDs).
  - Documented code for clarity and maintainability.

- **Telegram Group:** Add a section in the README with details on how to join the Telegram group you created for testing.

- **Reference:** You may refer to the [Hello Bot sample](https://core.telegram.org/bots/samples/hellobot) for initial guidance and also seek help from the [BotHouse Telegram Group](https://t.me/BotHouse).

#### Evaluation Criteria
- Functionality: Does the bot meet all the requirements and handle the ticket request and group management as specified?
- Code Quality: Is the code clean, well-documented, and organized?
- User Experience: Is the bot easy to interact with, and does it provide a seamless user experience?
- Creativity: Any additional features or enhancements beyond the basic requirements will be appreciated.

#### Preferred Languages
- PHP
- Node.js
- Python

#### Deadline
Please check the Deadline mentioned in the Email you recieved.

**Good luck!** If you have any questions, feel free to reach out.

---

### Suggested Folder Structure

Here is a suggested folder structure for your project in PHP:

```
CeyLabs-TGBot-EventRSVPBot/
│
├── src/
│   ├── main.php
│   ├── config.json
│   ├── database.json
│   └── utils/
│       ├── event_info.php
│       ├── registration.php
│       ├── group_invitation.php
│       └── database.php
│
├── tests/
│   └── test_main.php
│
├── README.md
├── .gitignore
└── composer.json
```

- `src/` contains the main application code.
- `utils/` contains utility scripts for handling specific functionalities like event info, ticket requests, group invitations, and database operations.
- `tests/` contains test scripts for your bot.
- `README.md` contains detailed instructions on setting up and running the bot.
- `.gitignore` specifies files and directories to be ignored by Git.
- `composer.json` lists dependencies for PHP.



## Join Our Telegram Group

We have created a Telegram group for testing, discussions, and support related to this project. Feel free to join the group to collaborate with other users and developers, ask questions, and share your feedback.

### How to Join

1. **Click the Invite Link:** Use the following link to join our Telegram group: [Join Telegram Group](https://t.me/+9YbDLZHLxYs5NDZl)
2. **Follow the Instructions:** If you don't already have a Telegram account, you will be prompted to create one. If you already have an account, you will be redirected to the group.

We look forward to your participation and contributions!

