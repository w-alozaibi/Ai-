# AI Recipe Generator

An AI-powered recipe generator that creates recipes based on available ingredients using OpenAI's GPT model.

## Features

- Generate recipes from available ingredients
- Clean and modern user interface
- Real-time recipe generation
- Example recipe combinations
- Responsive design

## Setup

1. Clone the repository:
```bash
git clone https://github.com/yourusername/ai-recipe-generator.git
cd ai-recipe-generator
```

2. Create a `.env` file in the root directory with your OpenAI API key:
```
OPENAI_API_KEY=your_api_key_here
OPENAI_MODEL=gpt-3.5-turbo
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_TOKENS=1000
DEBUG_MODE=false
```

3. Make sure you have PHP installed with the following extensions:
- curl
- json
- mbstring

4. Place the project in your web server's document root (e.g., htdocs for XAMPP)

5. Access the application through your web browser:
```
http://localhost/ai-recipe-generator/
```

## Usage

1. Enter your ingredients (comma-separated) in the input field
2. Click "Generate Recipe" or use one of the example combinations
3. Wait for the AI to generate your recipe
4. View the generated recipe with ingredients and instructions

## Security

- API keys are stored in environment variables
- Input validation and sanitization
- XSS protection
- Error handling

## License

MIT License 