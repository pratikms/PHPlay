# CSV Evaluator

## Problem Statement

Write a command line program to which parses a spreadsheet-like CSV file and evaluates each cell by these rules:

1. Each cell is an expression to be summed
2. Each token in the expression will be separated by one or more spaces
3. One cell can refer to another cell with the {LETTER}{NUMBER} notation (e.g. “A2”, “B4”– letters refer to columns, numbers to rows)

The program should print out the results in CSV format of the same dimensions containing the results of evaluating each cell to its final value. If any cell is an invalid expression, then for that cell only print #ERR.

For example, for the following CSV input:

| | | |
| --- | --- | --- |
| 5 | 3 A1 | B1 B1 |
| A1 | D5 | B2 1 |

...output (in csv format!):

| | | |
| --- | --- | --- |
| 5 | 8 | 16 |
| 5 | #ERR | #ERR |

There are other error conditions that your implementation should detect and handle in a manner you think appropriate.

**The program must run from the command line, accept a file as an input argument and print the results to STDOUT.**

## Note

The solution provided requires the following extensions to be installed / enabled in order to run:

- php-zip
- php-xml
- php-gd2
- mbstring