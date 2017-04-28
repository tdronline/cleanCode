# MageLess - Code Checker Interface

Code to Magento 2 Standards, use the magento 2 Code testing functionality and review code easyly.
Install Codesniffer and Pear needed for magento Code test module.

# How to use
Copy folder contents to your local web root and navigate to http://localhost/cleanCode/install

- Add the file paths properly. Use the directory paths with drive letter.
- To install pear and Code Sniffer please open Command Prompt as Administrator. You need admin rights to modify php.ini file.
- Follow the instructions on CMD to install pear, and Code Sniffer, Install pear as system. So you can use the pear installation in future updates and development.
- Next download the "run.bat" file from the given link. Run it after Pear and Code Sniffer installation. It will generate the report after few minuets.
- Click View report to view your Error report. You have to run this after each correction to your Less source files to update the Magento Less Report.

# Add new projects

You can add multiple projects to the code checking module. Simply click Add New Project Link on top navigation and Fill the paths and Download the ".bat" file for that project.

Any time you can run the command manually, in the project folder run command

bin/magento dev:tests:run static

to create the less report from magento.

*In magento 2.1
If the Error textfile is not generated when you run the batch file Please check the files in following folder are empty in Magento 2.1.