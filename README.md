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
- Execute [Game](src/Game.php) with stable parameters inspired by [GameRunner.php](src/GameRunner.php)
    - In case that the [approvals](tests/approvals) are create using `docker`, 2 commans are necessary to be executed from the host to edit the files via IDE - change user for the local machine `USER` name
        - ```
            sudo chown -R `USER`:`USER` tests/approvals
            sudo chmod 666 tests/approvals/*
          ```
    - First execution with a `Game` with `3 users` and just one roll with `1` as result
    - At this point the test cover the `56.25%` of the `Game` class!
    - We should be catious because most of the coverage can be accidental coverage which would not protect us from applying breaking changes through the refactor process
    - Nevertheless the goal now would be to try to cover 100% the `Game` code. Then, the coverage will be examine introducing breaking changes to the code and in case the test do not cover them, the test will be extended to cover them
    - Also might be that the `approval tests` either cannot cover everything or it were complicated to cover an specific case. Then applying a single unit test would be considered for this specific cases
- Extend the test for a second roll
    - The coverage increases from `56.25%` -> `66.67%`
    - At this point, we have all the needed inspiration from `GameRunner.php`. The file can be removed.
