<?php
 
/*
 * Example PHP implementation used for the index.html example
 */
 
// DataTables PHP library
include( "Datatable/lib/DataTables.php" );
 
// Alias Editor classes so they are easy to use
use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
 
// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'balance' )
    ->fields(
        Field::inst( 'codeActif' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A first name is required' ) 
            ) ),
        Field::inst( 'codeArticle' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A last name is required' )  
            ) ),
        Field::inst( 'codeRegate' ),
        Field::inst( 'numeroSerie' )
            ->validator( Validate::email( ValidateOptions::inst()
                ->message( 'Please enter an e-mail address' )   
            ) ),
        Field::inst( 'statut' ),
        Field::inst( 'dateVerification' ),
        Field::inst( 'localisation' )
            ->validator( Validate::numeric() )
            ->setFormatter( Format::ifEmpty(null) ),
        Field::inst( 'utilisation' )
            ->validator( Validate::numeric() )
            ->setFormatter( Format::ifEmpty(null) ),
        Field::inst( 'tranche' )
            ->validator( Validate::dateFormat( 'Y-m-d' ) )
            ->getFormatter( Format::dateSqlToFormat( 'Y-m-d' ) )
            ->setFormatter( Format::dateFormatToSql('Y-m-d' ) ),
        Field::inst('idEntite')
            ->message('idEntite requis'),
        Field::inst('idModele')
    )
    ->process( $_POST )
    ->json();