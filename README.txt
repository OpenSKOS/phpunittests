-- Testing OpenSkos --

1) If you just have got the sources from a repository, you need to download the libraries and add
the corresponding dependencies. It is done simply by running "composer" in the <sources-directory>

php composer.phar install

It picks up "composer.json" file containing the list of necessary libraries. This file  must be in 
the repository. The "composer.phar" may be but do not have to be in the repository,  
and you can download it from elsewhere.

After running the command, a directory "vendor" is created. It contains the necessary libraries.

2) You need to adjust the file <sources-directory>/OpenSkos/phpunit.xml. 
It contains the constants describing the test user to authenticate, and the data which are used
as expected values (originals) to compare with the obtained values. You need to pick up
a concept in the database, against which you will run the tests, and fill in the values of the constants
for this concept.

3) You may run tests from <sources-directory>/vendor/phpunit/phpunit subdirectory.  
The example command looks like 

    ./phpunit -c /apitest/OpenSkos-1-picturae/phpunit.xml /apitest/OpenSkos-1-picturae/ImportExportTest.php 
     
OR 
    ./phpunit -c /apitest/OpenSkos2/phpunit.xml /apitest/OpenSkos2/CreateConcept2Test.php 

4) example fusekui query (should not be necessarily URL-encoded) http://192.168.99.100:3030/openskos/query?query=select%20%20?s%20?p%20?o%20%20where%20{%20?s%20?p%20?o%20.%20}