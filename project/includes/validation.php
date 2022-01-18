<?php
// function to validate profile information provided by user
// returns an array with the validated data, and errors
function validateProfile(array $data, array $errors) {
    //check if fields are empty
    if ($data['first_name'] == '') {
        $errors['first_name'] = "Voer uw voornaam in";
    }
    if ($data['last_name'] == '') {
        $errors['last_name'] = "Voer uw achternaam in";
    }
    if ($data['street'] == '') {
        $errors['street'] = "Voer de straat waar u woont in";
    }
    if ($data['house_number'] == '') {
        $errors['house_number'] = "Voer uw huisnummer in";
    }
    if ($data['postal_code'] == '') {
        $errors['postal_code'] = "Voer uw postcode in";
    }
    if ($data['city'] == '') {
        $errors['city'] = "Voer uw woonplaats in";
    }
    // validate user input

    // first names can be a maximum of 50 characters.
    // to help user trim whitespace from beginning and end, and capitalize first letter.
    $data['first_name'] = ucfirst(trim($data['first_name']));
    if (strlen($data['first_name']) > 50) {
        $errors['first_name'] = "Voornaam te lang, maximaal 50 tekens.";
    }
    // last names can be a maximum of 75 characters.
    // to help user trim whitespace from beginning and end
    $data['last_name'] = trim($data['last_name']);
    if (strlen($data['last_name']) > 75) {
        $errors['last_name'] = "Achternaam te lang, maximaal 75 tekens.";
    }
    // check if email is in a valid emailadress format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Voer een geldig emailadres in";
    }
    // street names can be a maximum of 75 characters
    // to help user trim whitespace from beginning and end, and capitalize first letter
    $data['street'] = trim($data['street']);
    if (strlen($data['street']) > 75) {
        $errors['street'] = "Straatnaam te lang, maximaal 75 tekens.";
    }
    // house numbers can be a maximum of 7 characters
    // and must start with, and include, a number
    // to help the user trim whitespaces from the beginning and end
    $pattern_has_number = "/[0-9]/";
    $pattern_begin_with_number = "/^[0-9]/";

    $data['house_number'] = trim($data['house_number']);
    if (strlen($data['house_number']) > 7) {
        $errors['house_number'] = "Huisnummer te lang, maximaal 7 tekens.";
    }
    if (!preg_match($pattern_has_number, $data['house_number'])) {
        $errors['house_number'] = "Huisnummer moet tenminste één nummer bevatten.";
    }
    if (!preg_match($pattern_begin_with_number, $data['house_number'])) {
        $errors['house_number'] = "Huisnummer moet beginnen met een nummer.";
    }
    // postal code must match pattern '1234AB'
    // therefore it can be a maximum of 6 characters (implicitly enforced)
    // to help user strip (white)spaces and make letters uppercase.
    $pattern = "/^[0-9][0-9][0-9][0-9][a-z][a-z]$/i";
    $data['postal_code'] = strtoupper(str_replace(' ', '', $data['postal_code']));
    if (!preg_match($pattern, $data['postal_code'])) {
        $errors['postal_code'] = "Voer een geldige postcode in. bijv. 1234AB";
    }
    // city names can be a maximum of 60 characters.
    // to help user strip whitespaces from beginning and end
    $data['city'] = trim($data['city']);
    if (strlen($data['city']) > 75) {
        $errors['city'] = "Woonplaats te lang, maximaal 60 tekens.";
    }
    // return array with data and errors
    return array('validated_data' => $data, 'errors' => $errors);
}

// function to validate product information provided by admin
// returns an array with the validated data, and errors
function validateProduct(array $data, array $errors) {
    //check if fields are empty
    if ($data['name'] == '') {
        $errors['name'] = "Voer een productnaam in";
    }
    if ($data['description'] == '') {
        $errors['description'] = "Voer een beschrijving in";
    }
    if ($data['price'] == '') {
        $errors['price'] = "Voer de verkoopprijs in";
    }

    //validate the input

    // product names can be a maximum of 75 characters.
    // to help user trim whitespace from beginning and end, and capitalize first character.
    $data['name'] = ucfirst(trim($data['name']));
    if (strlen($data['name']) > 75) {
        $errors['name'] = "Productnaam te lang, maximaal 75 tekens.";
    }
    // Description has an unlimited character limit
    // to help user trim whitespace from beginning and end.
    $data['description'] = ucfirst(trim($data['description']));

    // price needs to be a number
    // price cannot be negative
    if (!is_numeric($data['price'])){
        $errors['price'] = "Verkoopprijs is geen nummer.";
    }
    if ($data['price'] < 0) {
        $errors['price'] = "Verkoopprijs kan niet negatief zijn.";
    }

    $data['visble'] = $data['visible'];

    return array('validated_data' => $data, 'errors' => $errors);
}

function validateCoupon(string $couponcode, array $errors, $id = null) {

}

