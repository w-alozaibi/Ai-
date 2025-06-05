<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Recipe Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .recipe-card {
            margin-top: 30px;
            padding: 25px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            animation: fadeIn 0.5s ease;
        }
        .recipe-details {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .recipe-details p {
            margin: 5px 0;
            flex: 1;
            min-width: 200px;
        }
        ul, ol {
            padding-left: 25px;
        }
        li {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        .examples {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: 8px;
        }
        .examples h3 {
            color: #2c3e50;
            margin-top: 0;
        }
        .example-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .example-tag {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .example-tag:hover {
            background-color: #45a049;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>AI Recipe Generator</h1>
        <form id="recipeForm" action="process.php" method="POST">
            <div class="form-group">
                <label for="ingredients">Enter ingredients (comma-separated):</label>
                <input type="text" id="ingredients" name="ingredients" 
                       placeholder="e.g., chicken, rice, tomatoes, onions" required>
            </div>
            <button type="submit" id="submitBtn">Generate Recipe</button>
        </form>

        <div class="examples">
            <h3>Try these combinations:</h3>
            <div class="example-tags">
                <span class="example-tag" onclick="useExample('chicken, rice, tomatoes, onions')">Chicken & Rice</span>
                <span class="example-tag" onclick="useExample('pasta, garlic, olive oil, parmesan')">Pasta Primavera</span>
                <span class="example-tag" onclick="useExample('beef, potatoes, carrots, onions')">Beef Stew</span>
                <span class="example-tag" onclick="useExample('salmon, lemon, dill, butter')">Lemon Salmon</span>
            </div>
        </div>

        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <p>Generating your recipe...</p>
        </div>
    </div>

    <script>
        function useExample(ingredients) {
            document.getElementById('ingredients').value = ingredients;
        }

        document.getElementById('recipeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            
            // Disable button and show loading
            submitBtn.disabled = true;
            loading.style.display = 'block';
            
            // Submit the form
            fetch('process.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.text())
            .then(html => {
                // Remove any existing recipe or error
                const existingRecipe = document.querySelector('.recipe-card');
                const existingError = document.querySelector('.error-message');
                if (existingRecipe) existingRecipe.remove();
                if (existingError) existingError.remove();
                
                // Add the new content
                const container = document.querySelector('.container');
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                container.appendChild(tempDiv.firstElementChild);
            })
            .catch(error => {
                const container = document.querySelector('.container');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerHTML = `<p>Error: ${error.message}</p>`;
                container.appendChild(errorDiv);
            })
            .finally(() => {
                // Re-enable button and hide loading
                submitBtn.disabled = false;
                loading.style.display = 'none';
            });
        });
    </script>
</body>
</html>



