### Init development environment
The development environment require `docker` installed

Run the following commands:

```
make build
make up
make install
```
### Refactor strategy
- The first step should be have a good test coverage of the code before the refactor get started
    - Since the code is very tangled, using tools such as [approval tests](https://github.com/approvals/approvaltests.php) could help
- Once a good coverage has been achieved by tests, the refactor process can begin
    - The idea is to do small steps for each refactor, doing microcommits every time a success refactor has been done to set a safe point to return
    - Every time that a refactor is done, the tests are executed and the code coverage is scrutinized
- At this point, branches would be created to explore different solutions and the taken decisions would be detailed explained at this `README.md` file

### Testing with approval testing 
- Since the code output string values in the stdout which are mixed with the calculation and also given the couplement code degree, using [approval tests](https://github.com/approvals/approvaltests.php) can help into quick cover the code with unit tests without overthink
- The idea would be to run `Game` executions, capture their result into a file and check the coverage 
- The [GameRunner.php](src/GameRunner.php) file can be used as inspiration to implement the approval testing

#### Steps
- add [approval tests](https://github.com/approvals/approvaltests.php) composer library
- implements approval tests using [GameRunner.php](src/GameRunner.php)
- Unfortunatelly [GameRunner.php](src/GameRunner.php) uses random values to execute the [Game](src/Game.php) genrating this way random response
- Commiting the results anyway and fix this problem in the next iterations