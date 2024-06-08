
## Instructions on How to Set Up and Run the Bot

### Prerequisites
- PHP installed on your machine.
- Composer installed for dependency management.
- A Telegram bot token obtained from BotFather.
- A publicly accessible webhook URL.


### Steps to Set Up the Bot

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/kumuduwije/CeyLabs-Intern-TGBot-EventRSVP/tree/bot-dev
   cd CeyLabs-TGBot-EventRSVPBot
2. **Install Dependencies:**
   ```
   composer install
   ```
3. **Configure the Bot:**

    Update the following content in config.json that contain inside src folder:

    ```
    {
      "bot_token": "YOUR_TELEGRAM_BOT_TOKEN",
      "group_invite_link": "YOUR_TELEGRAM_GROUP_INVITE_LINK",
      "bot_username": "YOUR_BOT_USERNAME",
      "webhook_url": "YOUR_WEBHOOK_URL",
    } 
    ```
### Run Tests:
   
   To run the tests, use the following command:
  ```
    vendor/bin/phpunit tests/
  ```

## Join Our Telegram Channel

We have created a Telegram group for testing, discussions, and support related to this project. Feel free to join the group to collaborate with other users and developers, ask questions, and share your feedback.

### How to Join

1. **Click the Invite Link:** Use the following link to join our Telegram channel: [Join Telegram Channel](https://t.me/kayeventbot)
2. **Follow the Instructions:** If you don't already have a Telegram account, you will be prompted to create one. If you already have an account, you will be redirected to the channel.
3. **Bot Commands:** You can see the list of bot commands in the channel and the link for the bot is pinned above.

We look forward to your participation and contributions!