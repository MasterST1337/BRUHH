<?php
/*
 * Indstillinger til genkend.php. Denne fil indeholder jeres hemmelige API-nøgle.
 *
 * Bedst: læg filen én mappe OVER public_html, så den slet ikke kan nås udefra.
 * Ellers: læg den ved siden af genkend.php. Fordi den ender på .php, viser
 * serveren aldrig indholdet, men del den ikke med nogen.
 */

// Nøglen fra platform.claude.com → Settings → API keys (begynder med sk-ant-)
define('ANTHROPIC_API_KEY', 'INDSÆT-JERES-NØGLE-HER');

// Kun hvis nøglen gælder for flere workspaces: workspace-ID'et. Ellers lad den være tom.
define('ANTHROPIC_WORKSPACE_ID', '');

// Jeres domæne(r). Kun kald fra disse sider bliver besvaret.
define('GENKEND_DOMAENER', ['isakram.dk', 'www.isakram.dk']);

// Loft over forbrug
define('GENKEND_PR_TIME', 20);    // billeder pr. besøgende pr. time
define('GENKEND_PR_DOEGN', 300);  // billeder i alt pr. døgn

// Model. Haiku er hurtig og billig og god nok til at kende løg fra gulerødder.
define('GENKEND_MODEL', 'claude-haiku-4-5-20251001');
