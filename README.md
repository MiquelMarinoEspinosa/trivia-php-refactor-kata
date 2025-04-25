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
- Extend the test to a second roll
    - The coverage increases from `56.25%` -> `66.67%`
    - At this point, we have all the needed inspiration from `GameRunner.php`. The file can be removed
- Extend the test to a third roll
    - Coverage increased from `66.67%` -> `70.83%`
    - It has been noticed that for every roll the coverage increases lesser than the previous roll
        - `56.25%` -> `66.67%` -> `70.83%`
    - For now the strategy will continue being the same up until the code coverage does not increase more
    - At that point, the not covered code will be analyzed to figure it out which is the best way to cover it

- Extend the test to a fourth roll
    - At this point the coverage for the first time decreases from `70.83%` -> `66.67%`
    - Time to analyze the not covered code from the previous iteration to find another strategy to add more coverage than just adding random roll
    - The missing coverage is related mainly to the `$this->inPenaltyBox[$this->currentPlayer]` condition which never is met. Also there is a method name `isPlayable` which is never called either in the `Game` or in the `GameRunner`
    - Curiously with the 4 `roll` this condition should be met. Checking right now event though it has been observed that the coverage decreases
    - Coverage increased from `70.83%` -> `78.12%`!
        - The trick was to add 2 `wasCorrectlyAnswered` instead of 1
- Extend the test to a fifth roll
    - start with a correct answer
    - Promising because the result has `Pat is getting out of the penalty box`. Let's check the coverage ^^
    - Yay! coverage extended from `78.12%` -> `97.92%`
    - As expected the message was related with the not covered penalty related code
    - This has been the code coverage evolution so far
        - `0%` -> `56.25%` -> `66.67%` -> `70.83%` -> `78.12%` -> `97.92%`
    - Next iteration take a look into the yet not covered code
- Code not covered yet
    - `isPlayable` method might be removed in this small context. However, let's asume that the code is integrated in a bigger context. Therefore, the method can be called by other client classes
        - This case can be easily covered with some specific unit tests for this method 
    - There is another line of code at `askQuestion()` regarding this condition `if ($this->currentCategory() == "Pop"`
        - It is posible that with some few more iterations on the `roll` test, the line can eventually be covered. Let's see ^^
- Let's add coverage to the `isPlayable` method first which seems the easiest code to cover
    - Add test when 0 players have been added, the game is not playable
        - Coverage increased from `97.92%` -> `98.96%`
        - The rest of the tests for `isPlayable` will not increase the coverage. Therefore, it will cover more business logic and they are easy to be added
    - Add test when 1 players have been added, the game is not playable
        - These unit tests would not check the output since it is already checked on the approval tests and also because it is not their goal. Nevertheless, in case of considering the check essential, easily could this test turned into approval tests
        - Same coverage `98.96%`
    - Add test when 2 players have been added, the game is playable
        - Same coverage `98.96%`