```markdown
# PHP Poll System

A versatile and user-friendly poll system built with PHP, JavaScript, HTML, and CSS, utilizing CSV files as a lightweight local database. This application allows users to create their own polls, share them with others, and view poll results through an intuitive dashboard.

## Features

- **Create Custom Polls:** Users can create polls by providing a question and multiple options.
- **Unique Poll IDs:** Each poll is assigned a unique identifier for easy sharing and tracking.
- **Voting Mechanism:** Users can vote on polls by entering their name and selecting an option.
- **Prevent Multiple Votes:** Ensures that each user can vote only once per poll based on their name.
- **Dashboard:** A centralized dashboard to view all existing polls, create new ones, and navigate to voting or results pages.
- **Real-Time Results:** Displays poll results dynamically using Chart.js for a visual representation.
- **CSV-Based Storage:** Utilizes CSV files to store poll data and votes, eliminating the need for a complex database setup.
- **Responsive Design:** Clean and responsive UI for seamless user experience across devices.

## Installation

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yasinULLAH/PHP-poll-system.git
   ```

2. **Navigate to the Project Directory:**
   ```bash
   cd PHP-poll-system
   ```

3. **Set Up Permissions:**
   Ensure that the web server has read and write permissions to the directory, especially for creating and updating CSV files.

4. **Run the Application:**
   - Deploy the project on a PHP-supported web server.
   - Access the application via your web browser.

## Usage

1. **Access the Dashboard:**
   Navigate to the application's URL to access the main dashboard where you can view existing polls and create new ones.

2. **Create a New Poll:**
   - Enter a poll question.
   - Provide at least two options separated by commas.
   - Click on "Create Poll" to generate a new poll.

3. **Share the Poll:**
   - Each poll has a unique ID which can be shared with others.
   - Users can navigate to the voting page using the provided links.

4. **Vote on a Poll:**
   - Enter your name.
   - Select one of the available options.
   - Submit your vote. Each user can vote only once per poll.

5. **View Poll Results:**
   - Navigate to the "View Results" section from the dashboard or after voting.
   - View real-time results displayed in a bar chart.

## Technologies Used

- **PHP:** Server-side scripting for handling form submissions, data storage, and retrieval.
- **JavaScript:** Enhances interactivity and handles dynamic chart rendering.
- **HTML & CSS:** Structures and styles the user interface for a clean and responsive design.
- **Chart.js:** Renders visual charts for displaying poll results.
- **CSV Files:** Acts as a simple local database for storing polls and votes.

## License

This project is licensed under the [MIT License](LICENSE).

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any enhancements or bug fixes.

## Acknowledgements

- Inspired by the need for simple and efficient polling systems without the overhead of complex databases.
- Utilizes [Chart.js](https://www.chartjs.org/) for elegant and responsive charting.

```
