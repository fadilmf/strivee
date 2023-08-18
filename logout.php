<?php

session_start();
// Menghapus semua session
session_destroy();
// Mengredirect user ke login.php
header("Location: /login.php");
