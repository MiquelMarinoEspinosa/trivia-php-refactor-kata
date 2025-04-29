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
- Extend approval test the test to a sexth roll
    - still `science` question :/
    - Same coverage though `98.96%`
    - Let's try adding a seventh roll
    - `Pop` question in the result. It looks promising ^^
    - Fixing the test copying the result and checking the coverage right away O.O
        - `100.00%` coverage |:D
- Considerations
    - Notice that just with a few unit tests have been able to covered the 100% of a very coupled code
    - The approval test process has facilitated achieving this goal since it was trial and error, copying the values to the `approve` file making the testing process really easy
    - Coverage evolution in `7` iterations
        - `0%` -> `56.25%` -> `66.67%` -> `70.83%` -> `78.12%` -> `97.92%` -> `98.96%` -> `100.00%`
    - Some could argue that this can be a little bit `black box testing` which does not help to understand the code. That is true in a sense. However, at this point the main goal is to cover the code to be able to apply a safe refactor. This strategy facilitates the process of covering the code. During the refactor process, there will be the chance to understand the code
    - Once the refactor has finished, the `approval tests` can either be removed or stay. They could be replaced by `unit tests` wich will help to understand the code
    - Now is time to introduce breaking changes to validate the correctness of the coverage as well as to evaluate how robust the current tests are
- Next iteration: change the code to provoke breaking changes to see whether the current tests cover this breaking changes
- Current code coverage analysis
    - The majority of the code is well covered by the current test. However, have been found some code that even changed the test has not detected the changes
        - Some of the code is because is implemented with a conditional without brackets. Nevertheless, the coverage indicates that the code is covered by the unit tests. Here there are the found samples
            - `if ($this->places[$this->currentPlayer] > 11) $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;`
            - `if ($this->places[$this->currentPlayer] == 0) return "Pop";`
            - `if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 1`
            - For these cases will be added the extra brackets to analyze the coverage and cover the code which is not covered yet 
        - There are other cases which needed to be analyzed in a deeper way
            ```
                else {
	                $this->currentPlayer++;
	                if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
	                return false;
                }
            ```
            
            ```
                function didPlayerWin() {
		            return !($this->purses[$this->currentPlayer] == 6);
	            }
            ```
        - It might be that all cases just need some more extra rolls to be executed and cover them with those missing rolls
- add brackets to all `if` conditions and cover the ones that are not covered yet
    - add brackets to `if` conditions
        - The coverage has been reduced from `100.00%` to `90.99%` as expected ^^
        - Some conditions are related to the number of player. More specifically `num players > 11`
        - Let's add up to `11` rolls
            - Currently there are `7` rolls. Therefore, `5` extra rolls should be added
            - Adding extra rolls increased the coverage from `90.99%` -> `97.30%`
            - A commit will be done at this point
            - Another strategy that would worth exploring is adding more than `3` players to the game
- Adding more player to the `Game` at the `GameTest`
    - up to `6`. Current `3`
        - Less coverage `97.30%` -> `95.50%`
    - add more rolls to the game
        - `5` more
            - current `18`
            - Now `23`
        - A little bit more coverage
            - `95.50%` -> `96.40%`
    - The number of players does not seem to be a decisive factor in increasing the coverage. Let's stay on increasing/changing the rolls
- Remove number of players down to `3` again keeping the `23` rolls 
    - Coverage increased from `96.40%` -> `98.20%` ^^
    - Let's do a commit & push to have a safe point to return
- Change the last rolls configuration
    - Let's configure the last `5` rolls as `wrongAnswer`
        - Same coverage
    - Let's configure the last `5` rolls as `wasCorrectlyAnswered`
        - Yay! Increasing coverage from `98.20%` -> `99.10%`
- At this point there is just one line not covered related to the question turn. Without overthink too much, it should be easily covered with some extra rolls
    - Add `5` more extra rolls up to `28`
    - Yet the same coverage
    - Adding `3` more rolls up to `31`
    - Adding `3` more rolls up to `34`
    - Yet the same coverage
    - At this point and since it is just one line of code to be covered, it would be a better strategy to debug the `places` variable that the logic depends on to see how to reach the code missing to be covered
        - Since the game is very verbose on its current status, analysing the output from the `approval test` coulld also help to figure out how to cover the pending line
    - Rollback last changes
        - `99.10%` coverage
- Analyze the `places` status evolution
    - The idea is that at some point `$this->places[$this->currentPlayer]` should be `0` to cover the missing line at `currentCategory` method
    - Using the output
        - According to the [GameTest.testCreateGame.approved.txt](tests/approvals/GameTest.testCreateGame.approved.txt), the player 2 is close to have its places to `0` value
            - This is the message related to the player 2 places
                `Player2's new location is 8`
            - In case `Player2` get more than `11`, there is a logic at `roll` method which substract `12 places` for an specific player
            - Also according to the output, the last player who played was `Player2`
            - Therfore, adjusting the next `rolls` conditions after the places message could help us to create the conditions to cover the code
            - Change next rolls
                - Avoid `Player2` ending up at the penalty box changing its both previous and next rolls as correctly answered
            - It has been identified that the analysis was not fully right. The analysis should have been focused on the `Gold Coins` message
            - Nevertheless, the `Player2` is the one that is closer with currently `8 Gold Coins` according to the `Game` output
            - Since each roll with valid answer gives an extra coin to the player, `4` more rolls for `Player2` should cover the line
            - Since there are `3 Players`, the number of total `rolls` to be added should be `11`
                - `23` current rolls + `11` new rolls = `34` total rolls
            - Finally, is has been needed `39` rolls,`16` new rolls, to reach the `100%` coverage
            - Commit and push at this point to have a safe return point
- Next steps
    - Analyze introduce breaking changes to the code to confirm that the code coverage prevents from breaking changes during the refactor process
    - Analyze whether the test can be simplified reducing number of players from `3` down to `2`
        - If so, re-check the coverage introducing breaking changes 
- Introduce breaking changes to check the current code coverage
    - Almost all and more than the previous code coverage breaking changes have been detected by the current unit test configuration
        - `didPlayerWin` method breaking changes have been no detected
            - It is used internally at the and not called by the test yet
            - In further iterations call to the method can be added to check the value of the method an be covered by the main approval test
        - Some returns of some function which seems to be used for internal uses
            - It should be considered that may be the values would not be used in future iterations since this kind of design couple the client code with the server unless is expected the client to do nay kind of action depending on the result value
                - `add`
                - `wasCorrectlyAnswered`
                - `wrongAnswered` 
- Refactor `GameTest`
    - Change `testCreateGame` name for `testPlayGame`
    - Capture string outputs to reduce the output noise tests for the `add` test methods
- Refactor `GameTest` reducing number of players of the approval test down to `2` to simplify the test and also to reduce the current `39` rolls to an smaller number
    - The coverage will be re-checked to confirm that covers the breaking changes code
    - Reduce players to `2` without changing the current amount of `rolls`
        - Still `100%` coverage
    - Since it is one player missing, let's try reducing `1/3` of the rolls and check whether still there are `100%` coverage
        - 39/3 = 13
        - 39 - 13 = 26
        - Still `100%` coverage with `26` rolls
    - Presumably, it is very probable that at least `12` rolls by player would be needed to cover the logic 
        - That makes `24` rolls per game 
        - Currently, there are `26` rolls
        - Let's remove the last `2` rolls and check the coverage
            - Still `100%` coverage
        - It has been found that instead of having `24` rolls, there were `23` rolls being the first player turn. There fore this roll should be able to be removed and keep the 100% coverage. Let's remove it then
            - Still `100%` coverage with `22` rolls
- At this point having the approval test `2 players` and `22 rolls` with the `100%` coverage, let's try to blindly optimize the `approval tests` removing every `2 rolls` starting from the final ones
    - At each removal, the code coverage will be checked
    - Once has been detected that the coverage has been reduced, the optimization will stop at that point
    - Rolls removing process
        - Let's remove the last 2 `rolls` down to `20` rolls in total
            - Coverage still 100%
        - Next 2 last `rolls` to remove down to `18` rolls total
            - Code coverage reduced to `98.20%`
            - Placing back the last 2 rolls
            - Code coverage back to `100%`
    - The full optimizaton reduce the game `approval test` complexity from
        - `3` players to `2` players -> 1/3 less
        - From `39` rolls down to `20` roll -> almost < 1/2
- Even though the `approval test` could be more optimised, that would require some level of code analyze, something that has been done to reach the 100% coverage or more trial and test. However, at this point, it could be considered that the `approval test` is optimized enough as long as it covers the breaking changes
    - It should not be forgotten that the it takes `128 ms` to be executed and test consits of around `70` lines, which are understandable enough