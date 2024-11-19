<?php
###function to generate a token length 62 char
function generateToken($length = 62): string
{
    return bin2hex(random_bytes($length / 2));
}
