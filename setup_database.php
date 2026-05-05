<?php
require_once("data/includes/connection_inc.php");

$tables = [
    "tbl_artists" => "
        CREATE TABLE IF NOT EXISTS tbl_artists (
            artist_pk INT AUTO_INCREMENT PRIMARY KEY,
            artist_folder_name VARCHAR(255) NOT NULL,
            artist_name VARCHAR(255) NOT NULL
        )
    ",
    "tbl_series" => "
        CREATE TABLE IF NOT EXISTS tbl_series (
            serie_pk INT AUTO_INCREMENT PRIMARY KEY,
            serie_folder_name VARCHAR(255) NOT NULL,
            serie_name VARCHAR(255) NOT NULL,
            serie_artist_fk INT,
            FOREIGN KEY (serie_artist_fk) REFERENCES tbl_artists(artist_pk)
        )
    ",
    "tbl_images" => "
        CREATE TABLE IF NOT EXISTS tbl_images (
            image_pk INT AUTO_INCREMENT PRIMARY KEY,
            image_document_name VARCHAR(255) NOT NULL,
            image_serie_fk INT,
            FOREIGN KEY (image_serie_fk) REFERENCES tbl_series(serie_pk)
        )
    ",
    "tbl_tags" => "
        CREATE TABLE IF NOT EXISTS tbl_tags (
            tag_pk INT AUTO_INCREMENT PRIMARY KEY,
            tag VARCHAR(255) NOT NULL UNIQUE
        )
    ",
    "tbl_link_tag" => "
        CREATE TABLE IF NOT EXISTS tbl_link_tag (
            link_tag_pk INT AUTO_INCREMENT PRIMARY KEY,
            link_tag_image_document_name VARCHAR(255) NOT NULL,
            link_tag_tag_fk INT,
            FOREIGN KEY (link_tag_tag_fk) REFERENCES tbl_tags(tag_pk)
        )
    ",
    "tbl_artist_bio" => "
        CREATE TABLE IF NOT EXISTS tbl_artist_bio (
            artist_bio_pk INT AUTO_INCREMENT PRIMARY KEY,
            artist_bio_artist_name VARCHAR(255) NOT NULL,
            artist_bio_text TEXT
        )
    ",
    "tbl_serie_bio" => "
        CREATE TABLE IF NOT EXISTS tbl_serie_bio (
            serie_bio_pk INT AUTO_INCREMENT PRIMARY KEY,
            serie_bio_serie_name VARCHAR(255) NOT NULL,
            serie_bio_text TEXT
        )
    ",
    "tbl_image_bio" => "
        CREATE TABLE IF NOT EXISTS tbl_image_bio (
            image_bio_pk INT AUTO_INCREMENT PRIMARY KEY,
            image_bio_image_document_name VARCHAR(255) NOT NULL,
            image_bio_text TEXT
        )
    ",
    "tbl_artist_media" => "
        CREATE TABLE IF NOT EXISTS tbl_artist_media (
            artist_media_pk INT AUTO_INCREMENT PRIMARY KEY,
            artist_media_artist_name VARCHAR(255) NOT NULL,
            concerning VARCHAR(255),
            media_text TEXT
        )
    ",
    "tbl_artist_corrections" => "
        CREATE TABLE IF NOT EXISTS tbl_artist_corrections (
            artist_corrections_pk INT AUTO_INCREMENT PRIMARY KEY,
            artist_corrections_artist_foldername VARCHAR(255) NOT NULL,
            artist_corrections_artist_name VARCHAR(255) NOT NULL
        )
    ",
    "tbl_serie_corrections" => "
        CREATE TABLE IF NOT EXISTS tbl_serie_corrections (
            serie_corrections_pk INT AUTO_INCREMENT PRIMARY KEY,
            serie_corrections_serie_foldername VARCHAR(255) NOT NULL,
            serie_corrections_serie_name VARCHAR(255) NOT NULL
        )
    "
];

foreach ($tables as $table_name => $sql) {
    if (mysqli_query($conn1, $sql)) {
        echo "Table $table_name created successfully<br>";
    } else {
        echo "Error creating table $table_name: " . mysqli_error($conn1) . "<br>";
    }
}

echo "Database setup completed!";
?>
