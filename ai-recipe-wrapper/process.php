<?php
// Inclusief de AIWrapper klasse
require_once 'classes/AIWrapper.php';
require_once 'classes/Recipe.php';
require_once 'classes/RecipeFormatter.php';
require_once 'config/config.php';

function displayRecipe(Recipe $recipe)
{
    echo '<div class="recipe-card">';
    echo '<h2>' . htmlspecialchars($recipe->naam) . '</h2>';
    echo '<div class="recipe-details">';
    echo '<p><strong>Bereidingstijd:</strong> ' . htmlspecialchars($recipe->bereidingstijd) . '</p>';
    echo '<p><strong>Moeilijkheidsgraad:</strong> ' . htmlspecialchars($recipe->moeilijkheidsgraad) . '</p>';
    echo '</div>';
    echo '<h3>Ingrediënten:</h3>';
    echo '<ul>';
    foreach ($recipe->ingrediënten as $ingredient) {
        echo '<li>' . htmlspecialchars($ingredient) . '</li>';
    }
    echo '</ul>';
    echo '<h3>Bereidingswijze:</h3>';
    echo '<ol>';
    foreach ($recipe->stappen as $stap) {
        echo '<li>' . htmlspecialchars($stap) . '</li>';
    }
    echo '</ol>';
    echo '</div>';
}

header('Content-Type: application/json');

// Controleer of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingredients'])) {
    try {
        // Valideer en verwerk de ingrediënten
        $ingredientsInput = trim($_POST['ingredients']);
        if (empty($ingredientsInput)) {
            throw new Exception("No ingredients provided");
        }
        // Splits de ingrediënten op komma's en verwijder witruimte
        $ingredients = array_map('trim', explode(',', $ingredientsInput));
        // Maak een nieuwe instantie van de AIWrapper
        $wrapper = new AIWrapper();
        // Verwerk de ingrediënten
        $wrapper->processInput($ingredients);
        // Haal het antwoord op
        $response = $wrapper->getResponse();
        
        // Try to parse the response as a recipe
        $formatter = new RecipeFormatter();
        $recipe = $formatter->tryExtractRecipe($response);
        
        if ($recipe) {
            // If we have a valid recipe, display it
            displayRecipe($recipe);
        } else {
            // If parsing failed, show the raw response
            echo '<div class="error-message">';
            echo '<p>Could not parse recipe. Raw response:</p>';
            echo '<pre>' . htmlspecialchars($response) . '</pre>';
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error-message">';
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
} else {
    echo '<div class="error-message">';
    echo '<p>Invalid request. Please provide ingredients.</p>';
    echo '</div>';
}
?>