<?php namespace Ors\Orsapi\Interfaces; 

/**
 * This is just a base search-api interface.
 * All search handlers must implement this interface,
 * because wrappers checks if handlers are instance of ITAG_SearchApiInterface.
 * 
 * In other words, this is the common denominator for all search-api wrappers.
 */
interface ITAG_SearchApiInterface {}