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
- Introduce breaking changes to check whether are caught by the tests
    - It has been found that the game generates `50` questions for each category and when the value is changed, the current test do not catch the breaking change
        - With already `20` rolls in the `approval test`, the maximum category number is `4`
        - Therefore, it would require a lot of rolls with `2` players to reach the `50th` category questions, overcomplicating the `approval test`
        - Not confirmed yet, but it might be that the current tests cover the rest of the logic as well as its posible breaking changes
        - Some could argue that this breaking change it is not needed to be covered because it is just a matter of being catious. I would disagree. As long as it can easily be covered by unit test, even though they can be coupled somehow with the code, I would rather at this initial point before starting the refactor to be as much backed by the tests as I could be
        - Let's use first the strategy of adding extra tests to check the number of category question when the `game` is first initialized to confirm that each category has initially got `50` number of questions
            - Breacking change captured with the new unit test
        - Neither code has been added nor change at `Game` to cover this breaking change
            - Currently the test access directly to the `Game` fields breaking encapsulation
            - In further iterations during the refactor process some encapsulation can be added to this fields which would make sense to expose to know the current number of questions by category from the client
    - Remove initialization arrays not covered
    - `$this->places[$this->currentPlayer] > 10` instead of `$this->places[$this->currentPlayer] > 11` not covered
        - it catches `$this->places[$this->currentPlayer] > 12` though
        - also it catches `$this->places[$this->currentPlayer] > 9`
        - removing `$winner = $this->didPlayerWin();` raises just a `warning`
        - removing `return $winner` does not catch
        - removing `return true;`
            - They are related to not use returned value
            - Let's add their assertions to the `wasCorrectlyAnswered` and `wrongAnswer` at the `approval test` once it has finished all the rest of the checks
        - returning allways `true` at `didPlayerWin` method not catched
- Next steps
    - Cover the return results for `wasCorrectlyAnswered` and `wrongAnswer`
        - implicitly should cover the `didPlayerWin`
    - Think why `$this->places[$this->currentPlayer] > 10` instead of `$this->places[$this->currentPlayer] > 11` is not covered
        - Even though the risk can be taken on not covered it since the rest of value ranges are covered
    - Try [mutation testing](https://infection.github.io/)
- Cover the return results for `wasCorrectlyAnswered` and `wrongAnswer`
    - Apply breaking changes to `wasCorrectlyAnswered`, `wrongAnswer` and `didPlayerWin`
        - There is just a breaking change at `wasCorrectlyAnswered` at the `else` condition branch when changing `return $winner;` for just `return true;` which was no catch
        - The rest of breaking changes which were not caught before, now they are covered by the new assertions at the `approval test`
        - This aspect is considered good enough covered to move on
- Array initializations
    - Since the array initializations values are always override and they are not use for the first computational values, they could even be removed in further iterations
    - In this firsts iterations, the array initializations will be kept before starting the refactor
- Check why `$this->places[$this->currentPlayer] > 10` instead of `$this->places[$this->currentPlayer] > 11` is not covered
    - Output analysis
        - Looking for `Gold Coins` messages at [GameTest.testPlayGame.approved.txt](tests/approvals/GameTest.testPlayGame.approved.txt)
        - Not much information from the previous search
        - Looking for `new location is 0` messages which indicate that the player's places have been reset
            - There are `3` messages
                - One is related to a player getting out of penalty box
                - The other `2` related to normal rolls with `6` value one and `2` value the other
                - Let's debug the player's places before reset them as well as they rolls
                    - At the first debug, it has been found that the `roll` has value `6`, the current player `places` is `12` meaning that the previous `places` where `6`
                    - At this point, the current situation makes me wonder that this logic cannot fully tested as the rest of the logic without `unit tests`
                        - Through the refactor process the program structure might change, splitting the code in a sense that the small units can be more thorough unit tested, enhancing the code coverage robustness
                        - It would require a some effort to know how to cover this code with `approval tests` being against of the principle on try to cover as much as it can to be safe to refactor even though not all breaking changes are fully covered
                        - Therefore, this logic will not fully tested at this stage of the process
- Try [mutation testing](https://infection.github.io/)
    - [Infection installation](https://infection.github.io/guide/installation.html)
    - Installation
        - [Composer global installation guide](https://infection.github.io/guide/installation.html#Composer)
        - Once composer install has finished, execute the following commands
            ```
                make shell
                export PATH=~/.composer/vendor/bin:$PATH
            ```
    - Usage
        - [Usage documentation](https://infection.github.io/guide/usage.html#Running-Infection)
        - Execute the following commands
            ```
                make shell
                infection
            ```
        - An error appeared which says
            ```
               The file "/tmp/infection/phpunitConfiguration.initial.infection.xml" does not pass the XSD schema
               validation.
                [Error] Element 'source': This element is not expected.
                in /app/ (line 6, col 0)
            ```
            - It is related to the new [phpunit.xml](phpunit.xml) configuration of the new `phpunit` library version
                - Removing the `source` node at [phpunit.xml](phpunit.xml) allow executing the `infection` program
                - The output has been added at [infection.mutationTesting.result.beforeRefactor.txt](./tests/infection/infection.mutationTesting.result.beforeRefactor.txt) file
    - Conclusion
        - The experience has been very good. Great tool to use!
        - Details
            - Some breaking changes introduced by the tool were already detected on the previous stage of introducing manual breaking changes to evaluate the coverage
                - changing greater than at `$this->places[$this->currentPlayer] >= 11`
            - Other were not found such as
                - `return false;` at `add` method
                    - This can easily be covered with extra assertions at the current automated tests
                - `$this->currentPlayer--;` at the `wasCorrectlyAnswered` else branch
                    - It might be that this case can be covered adding more players
                    - There is no intention now to cover this logic
                - change methods visibility to `protected`
                    - since a lot of methods are just called internally, it makes sense that this changes has not been covered
        - The most important indicator to take into account is the [Mutation Score Indicator MSI](https://infection.github.io/guide/index.html#Mutation-Score-Indicator-MSI)
            - It is `95%` in the current code
            - That means that the difference between the current code coverage - `100%` - and the MSI - `95%` - it is low and the tests effectivelly covers the code
                - From `182` mutants, there were `9` not detected and just `1` error :D
            - This provides extra trust on the current code coverage
- Next steps
    - Cover the `add` method with extra assertions to kill another mutant
    - Start the refactor code process ^-^
- Cover the `add` method with extra assertions to kill another mutant
    - One less scaped mutant
- Debug why `$this->currentPlayer--;` at the `wasCorrectlyAnswered` else branch mutant is not killed
    - As it was commented, the reason is because the `Game` has just `2` players
        ```
            $this->currentPlayer++;
			if ($this->currentPlayer == count($this->players)) {
				$this->currentPlayer = 0;
			}
        ```
    - When the `currentPlayer` is `1` and it is decremented its value, it has exactly the same value that when reach the maximum number of players, which is `0`
    - Adding an extra player should solve catch this mutation
    - Presumabily would imply adding extra rolls to keep the `100%` coverage
    - If so, it would worth it to add an extra small `approval test` to cover this logic
    - Adding a third player to the current `approval test`
        - Decreases the coverage from `100%` -> `96.40%` :/
    - Let's add an extra approval test with 3 `players` and just a few `rolls`. Let's start with `3` rolls for instance
    - Introduce the mutan breaking change
        - Manual breaking change covered :)
    - Execute `infection` mutation testing
        - One mutant more captured
        - `MSI` increased from `95%` -> `96%` :D
- At this point of the process, the code has been covered with `unit tests`. Therefore, we are ready to start with the refactor
    - A new branch called `refactor` will be created
    - The refactor decisions will be documented in the next section of this [README.md](./README.md) file just as it was documented the test process

### Refactor
#### Initial considerations
- After having reached the 100% coverage of the code the refactor can start
- It has been noticed among other aspects that genarally the game (`business`) logic is very coupled with (`presentation`) messages outputs. Therefore, the first refactor goal would be `decouple business from presentation logic`. To achieve this goal, very likely both logic will be placed at different `object classes`. That is a good practice to be able to modify these logics independently without the need of modifying the other logic
- It has also been found that neither `field and method visibility`, `return and parameters type hinting` nor in general best practices of `object orientation programming` have been applied. Duing the factor this aspekt will be enhanced
- Also a lot of `duplication code` has been found which will be remove through the refactor action

#### Refactor strategy
- The strategy will start applying quick wins in a baby steps manner
- The new design would slowly be introduced
- At every change the `automated tests` will be executed and the coverage `scrutinized`
- Once the refactor has been considered finished, the `mutation testing` will again be executed to analyse its output
- That said, let's start! :D

#### Refactor steps
- Let's start adding visibility to the `Game` methods to distinguish which ones are called from outside - `public` - from which ones are internally called - `private`
- It has also been found that the `echoln` function is outside the `Game` class
    - It might that this function is globally called by other clients. Nevertheless, it will be asumed that this function is just called at the `Game` class
    - Therefore, the method will be move to the `Game` class and declare it as `private`
- There are some format to fix to improve readability. That will be done using an external tool called [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
    - The tool has done a good job. Just some manual fixes would be needed
    - It has also added `public` visibility to the the `Game` fields. This change would be undone because I would like to do this one in the next iteration
- Add visibility to the `Game` class fields. By the default, they all should be `private`
    - In order to check the `50` questions rightly initialized, the `questions` array must be `public`
    - At this point, it can be used the [Asymetric Property Visibility](https://www.php.net/manual/en/language.oop5.visibility.php#language.oop5.visibility-members-aviz) introduced at the `8.4` php version. It consists in that a field can be `public` but its `setter` be `private`, not allowing its modification from outside. Here there is a sample implementation:
    ```
        public private(set) array $popQuestions;
    ```
    - The IDE force to add `type hinting` to these fields. In the next iteration, type hinting will be added to the rest of the fields
    - In further iterations a more elegant solution might be found. For now, it is ok
- Refactor test class introducing `setUp` method to instantiate at each test the `Game` class
- Refactor test class extract number questions into constant
- Add `type hint` to the `fields` at `Game` class
- Add `type hint` to the `parameters` and `return` to the `Game` `methods`
- Initialize `currentPlayer` field at `__construct` method
- Change `array` initialization to `[]` syntax
- Make `Game` class final
- Sort method to have `public` method first and `private` methods last
- At this point, I will take some time to analyze the current `Game` code structure in high level
    - The methods `roll` and `wasCorrectlyAnswered` are the ones with more indentation levels and code paragraph
    - During the testing phase, it has been identified some duplication code which could be remove
    - Let's start to extract paragraph codes into methods and see whether the duplication can be remove using this technique
- Before extracting any method, as explained at the beggining of this section, the aim would be decoupling `presentation` from `business` logic. Now the `presentation` rely on the current `business` status and displays the current status at every action took. The idea would be try to make all the actions at once and then display the information once all the actions had happened
    - To achive that, it would be needed to save some information in temp variables or fields
- Let's start decoupling `presentation` from `business` logic at `roll` method
    - Extract common logic presentation for both conditional branches at `roll` method removing this way some duplicated code
    - Now I am going to proceed to `duplicate conditional` to decouple `presentation` from `business` logic at `$this->inPenaltyBox[$this->currentPlayer]` condition
    - After these refactors, I can remove some extra duplicated `business` logic which is related to upgrade the `$this->places[$this->currentPlayer]`
    - Add a `safe guard` when player still stays at penalty box
    - Simplify penalty box `business logic` condition to reduce nesting levels
    - Simplify penalty box `presentation logic` condition to reduce nesting levels
        - Build the message via a `query` rather than using `variable` since I cannot inline the condition with a `ternary operator`
        - Take avantage of the method and apply `tell don't ask` principle locating the penalty box condition inside the new method as well as the echo
        - Create a new method to print the message keeping another to build the message
    - After taking a look at the code in github, I have realized that the `Game` class is not well formatted. Let's fix that
        - format `__construct` method
        - Eventually the whole class has been formated using a IDE command
    - Extract `presentation` `roll` method logic in two methods `pre` and `post` roll process
        - Extract initial `roll` presentation message into a new method called `printPreRollMessage`
        - Extract the last `roll` presentation message into a new method called `printPostRollMessage`
        - Extract `roll` `business logic` into a new method called `processRoll`
        - There are duplication code related to the penalty box condition which would like to be remove extrating it into a new method and use it as a `query`
            - Now the method is returning `false` for a certain condition making difficult to read. The condition will be turn into positive return in case the user is getting out of penalty box
        - Add penalty box condition at `printPostRollMessage` to apply `tell don't ask` principle
        - At this point the `roll` method `presentation` and `business` logic is decoupled enough to be able to relocate the logic even in other classes in further iterations
        - Let's do the same with the rest of the methods
- Decouple `wasCorrectlyAnswered` method its `business` from the `presentation` logic
    - Fix a bug in the text displaying `corrent` instead of `correct` when the answer `wasCorrectlyAnswered`
        - We asume that it is a typo
        - It would require to change the test message's expectation
        - This way some duplication can be removed once the typo has been fixed
        - Remove `answer was correct` duplication logic for a branch of the `if` condition and the `else` branch
            - As a rule of thumb, the `else` branches can be replaced by `safe guard early returns` making the code more readable and easier to understand :)
        - Save `currentPlayer` value into a `temp` variable for printing porpuses and `slide` variables grouping them at the `presentation` logic
        - Add parameter to ask `didPlayerWin` by player
            - It could have been inlined. However, the method's name enhance readability
        - Slide `business` logic at top of the method
        - Save `currentPlayer` at the beggining of the execution
        - replace `winner` `temp variable` into a `query`
        - extract answer correct print into a method
        - Rename `$currentPlayer` temp variable to `$player`
        - Duplicate penalty box condition to apply early return to avoid printing answer correct and simplify condition
        - Extract condition into a new method called `isCurrentPlayerInPenaltyBox`
            - Since the condition logic looked similar to the one implemented at `isCurrentPlayerGettingOutOfPenaltyBox` it has been tried to use the result method in this condition, at it worked! ^^
        - Extract the `business` logic into a new method called `processCorrectAnswer`
        - `Tell don't ask` at `answer correct` printing method
        - introduce `player` temp variable to remove conditions code duplication at `processCorrectAnswer` method
    - Just like the `roll` method in previous iterations, the `wasCorrectlyAnswered` `business` logic has been decoupled from its `presentation` logic, ready to be move to a new class if needed
- Next steps
    - Decouple `wrongAnswer` `business` logic from `presentation` logic
    - Explore other logic that should be remove
    - Introduce an `inline anonymous class` to start to relocate the `business` to a new class
- Review the current `roll` method logic to simplify it to just one method to `print` and another to `process` just like the `wasCorrectlyAnswered` method
    - The trick will be saving the `currentPlayer` in a `temp` variable before process it
    - Pass the `player` to the `print` method to use it for printing porpuses
    - `slide` 2 `print` methods together after the `rollProcess` method
    - Merge the `pre` and `post` method into unified `printRoll` method
- Split `wrongAnswer` into 2 methods: one to `print` and another to `process`
    - `extract method` print wrong answer
    - `extract method` process wrong answer
    - This iteration has been easier than the `roll` iteration since the code was not so much coupled with the field current values
- Split `add` method into `print` and `process`
    - `extract method` process
        - even though the `players` data structure store the players name, the data structure is used also for the `business` logic
        - It would be better this structure to be hold by the `business` class rather exposing methods to allow the `presentation` logic to print the right data
    - `extract method` print
- At this point, the `business` logic has been decoupled from the `presentation` logic at the same `Game` class. Now the intention will be move the logic into a separate new class. This process will be done via small steps which will be commented in this section
    - The first step will be to add `getters queries` to all that fields which the `presentation` logic access to print its values
        - `slide` print and process methods together
        - It has been identified in previous iterations that the `players` structure which stores the players names is used for both `presentation` and `business` logic. To remove dependencies, a new structure will be created to store the same information and it will be used at `process` methods
            - Create and use new players structure called `playersProcess` - temporary name
            - Update `playersProcess` at `processAdd` method and `players` just at `addPrint` method
            - Replace at `howManyPlayers` method to use `playersProcess` new data structure since it is just used for `process` methods
            - Replace all `process` methods to use `playersProcess` new data structure instead of `players` data structure
        - At this point, all methods which directly use a `field` will be refactor to use a `query` instead. That would help us to get ready to introduce the new class and start to move `business` logic to this new class
            - Replace `$this->currentPlayer` to access via `query` called `currentPlayer`
            - Add method to update `currentPlayer` without directly access to it
                - add this point `currentPlayer` is accessible just via methods, not directly accessible
                - Notice that the initializations are not using queries but access directly to the fields
            - Replace `$this->purses[$player]` access to a method called `pursesBy`
                - Add method to increment `purses`
            - Replace `$this->places` to a query called `currentPlayerPlaces`
                - Extract update `places` logic into a new method
            - Replace `$this->inPenaltyBox[$this->currentPlayer()]` for a query called `isCurrentPlayerInPenaltyBox`
                - Replace `$this->inPenaltyBox[$player]` for a query called `isPlayerInPenaltyBox`
                - Add method to update `inPenaltyBox` status
            - The `questions categories` will not be introduced any query because would stay in the end at the `presentation` class
            - Replace `isGettingOutOfPenaltyBox` direct access by a `query`
                - Create method to update `isGettingOutOfPenaltyBox` value
    - At this point of the refactor, all the shared variables have been limited their access via `queries` and `setters` methods. Therefore, it should be safe to move them into a new class. A new `anonymous class` will be created at `Game` class to start the `process` fields transition. At the end, the methods will be also be moved to this new anonymous class and a proper class will be created
        - Create `anonymous class` as `Game` field called `gameCalculator`
        - Move `playersProcess` field and methods
            - Moved `playersProcess` field
            - Moved partially `processAdd` method
            - Moved `howManyPlayers` method
            - Refactor `Game` to use the field methods from `gameCalculator`
        - Move `currentPlayer` field and methods
            - Moved `currentPlayer` field
            - Moved `currentPlayer`
            - Moved `nextPlayer`
            - Refactor `Game` to use the field methods from `gameCalculator`
        - Move `isGettingOutOfPenaltyBox` field and methods
            - Moved `isGettingOutOfPenaltyBox` field
            - Moved `isCurrentPlayerNowGettingOutOfPenaltyBox` method
            - Moved `setIsGettingOutOfPenaltyBox` method
            - Refactor `Game` to use this the field methods from `gameCalculator`
        - Move `inPenaltyBox` field and methods
            - Moved `inPenaltyBox` field
            - Moved `isCurrentPlayerInPenaltyBox` method
            - Moved `isPlayerInPenaltyBox` method
            - Moved `addCurrentPlayerToPenaltyBox` method
            - Refactor `Game` to use this the field methods from `gameCalculator`
        - Move `purses` field and methods
            - move `purses` field
            - move `pursesBy` method
            - move `increasePursesFor` method
            - Refactor `Game` to use this the field methods from `gameCalculator`
        - Move `places` field and methods
            - Move `places` field
            - Move `currentPlayerPlaces` method
            - Move `increaseCurrentPlayerPlacesBy` method
            - Refactor `Game` to use this the field methods from `gameCalculator`
    - At this point all `business` logic fields and their access methods have been moved to `gameCalculator anonymous class`. Also the methods that just use other `gameCalculator` methods can be also be moved before extracting the class to another class on its own, since they do not have any other dependency. They should be the ones with the `process` prefix. This way the current moved methods could their visibility be restricted
        - Create `isPlayable` method at `gameCalculator`
            - This method would not be removed from the `Game` since it is used by the client test. Neither I would like to expose `gameCalculator` to the test and I would like to apply `demeter law` in this case
            - At least the `howManyPlayers` method reduce one external dependency
        - Inline `processAdd` method at `add` method
        - Move `isCurrentPlayerGettingOutOfPenaltyBox` method at `gameCalculator`
        - Move `processRoll` method to `gameCalculator`
        - Move `processCorrectAnswer` method to `gameCalculator`
        - Move `processWrongAnswer` method to `gameCalculator`
        - Move `didPlayerWin` method to `gameCalculator` and `inline` it at `Game`
        - Create `GameCalculator` class, extract anonymous class and instantiate it at `Game`
            - Due to `strict_type` it was needed to fix the `inPenaltyBox` initialization to be a `boolean`
            - Fixing also spaces at end of line format
- At this point we have 2 `classes` more or less of the same size - between 150 and 200 lines - and more importantly, the `presentation` logic is decoupled from the `business` logic
    - Using an `anonymous inline class` at the beggining of the refactor made the process much more painless and efective
    - Next steps
        - Refactor `GameCalculator` restricting visibility of some methods
        - Refactor `GameCalculator` `process` methods to remove the word at the methods names
        - Check whether the `GameCalculator` can return the `currentPlayer` name to be able to remove the `players` data structure at `Game`
        - Check on `Game` class for further refactors
- Refactor `GameCalculator` restricting visibility of some methods, the ones which are note access by `Game` and sort the methods by visibility - `public` first
- Refactor `GameCalculator` `process` methods to remove the word at the methods names
- Refactor `GameCalculator` rename `playersProcess` name to `players` since does not colide with `players` field any longer
- Refactor `GameCalculator` rename `howManyPlayers` method's name to `numPlayers`
- Now that the visibility of some methods at `GameCalculator` has been reduced, it can easily be identified which ones are used not many times which deserves to have its own class and `inline` them, specially the ones that just have a single line of code and are just called in a single place
    - `inline isCurrentPlayerNowGettingOutOfPenaltyBox` method
    - `inline setIsGettingOutOfPenaltyBox` method
    - `inline addCurrentPlayerToPenaltyBox` method
    - `inline increasePursesFor` method
    - `inline increaseCurrentPlayerPlacesBy` method
- Check whether the `GameCalculator` can return the `currentPlayer` name to be able to remove the `players` data structure at `Game`
    - Make `numPlayers` method `public` at `GameCalculator` and use it at `Game`
    - Create a public method at `GameCalculator` called `nameBy` and passing the `player` parameter returning the player's name and use it at `Game`. Remove at `Game` the `players` data structure
    - Although I do not really fancy the idea of storing players names at `GameCalculator`, I must admit that this data structure is mainly used at the `GameCalculator` and just exposing a simple method to be used at `Game`, the data structure duplication can be avoid
    - Remove `purses` innecessary initialization at `GameCalculator`
    - `type hint` `GameCalculator` field at `Game` class
    - Extract `magic numbers` at `GameCalculator` into constants
    - At this point, the `GameCalculator` refactor class can be considered as finished. Let's take a look at `Game` class
        - At the `__construct` method there is some logic regarding the questions creation that I think could be extracted some how into a private method
        - The `askQuestion` method also can be simplified and it might be used a `match` operator for instance
        - Similar situation for `currentCategory` method
        - The rest look like kind of ok
        - Let's do the changes one per iteration :D
- Refactor questions initialization at `Game` class `__construct` method
    - `extract method` for questions initialization
    - refactor `$i` temp variable to `$question`
    - extract `for` body into a new method called `createCategoryQuestionsBy` and rename `$question` to `$numQuestion`
    - Good enough. To finish I will remove the extra parentesis of the strings and `inline` the `createRockQuestion` method
- Just discovered that this logic `$roll % 2 != 0` which is duplicated in both `Game` and `GameCalculator`, should just belong to `GameCalculator`. To do so, `GameCalculator` needs to expose a public method to return the result of this expresion's evaluation
    - create a `private` method at `GameCalculator` called `isGettingOutOfPenaltyBoxBy` passing the `roll` and use it at `GameCalculator`
    - Make the method from the previous item public and use it at `Game` class
- `extract constant` max num questions `magic number` at `Game` class
- Next steps
    - The `askQuestion` method also can be simplified and it might be used a `match` operator for instance or a map of numbers for each question
    - Similar situation for `currentCategory` method
    - Execute `mutant testing`
    - Close the refactor exercise with final considerations about the process and how much I like the exercies ^-^
- Refactor `askQuestion` method to use `match` operator
    - `extract variable` called `question` to use a single `echo` instruction
    - `extract method` get the current question from `askQuestion`
    - introduce `match` operator at `currentQuestion` method
        - introduce `Pop` and `default` cases
        - add `Science` case
        - add `Sports` case
        - add `Rock` case
        - remove `question` temp variable and directly return the `match` result
- Refactor `currentCategory` method to use the `match` operator
    - Introduce `category` temp variable
    - Introduce `match` operator with first `0` `Pop` category and `default` `Rock` cases
    - Add rest of `Pop` cases at `match` operator
    - Introduce `1` `Science` category at `match` operator
    - Add rest `Science` cases
    - Introduce `2` `Sports` case at `match` operator
    - Introduce the rest of `Sports` cases
    - Remove `category` temp variable and directly return the `match` result
- Remove not use `default` case at `currentQuestion` method at `match` operator just to make clear that the case is not used even though the coverage does not reflect this fact
- Notice that during all this refactor the `GameCalculator` has not been modified, meaning that no `business` logic could not be affected by the refactor of the `presentation` logic
- At this point, I have realized that the `3` questions data structure are kind of redundant and can be merge in a single `questions` structure data. That would also simplify - even remove - the `currentQuestion` and `currentCategory` methods
    - Encapsulate the access to the questions structure directly used by an unit test
        - Create `totalNumQuestions` and refactor `GameTest` to use it at an unit test
        - Make the questions data structure `private`
        - Introduce `questions` data structure and initialize `Pop` questions
        - Use the new `questions` structure at `currentQuestion` method for `Pop` questions
            - Notice that it is retrieving the question dynamically directly using the `$this->currentCategory()` value as index
        - Use the new `questions` structure at `totalNumQuestions` method for `Pop` questions
        - Introduce `Pop` constant category
        - Remove phpunit tests warning initializing `purses` again at `GameCalculator` and add `phpunit` configuration to display warnings
        - Remove `popQuestions` data structure
        - Initialize `Science` questions at the new `questions` data structure
        - Use the new structure for `Science` questions at `totalNumQuestions` method
        - Use the new structure for `Science` questions at `currentQuestion`method
        - `extract constant` `Science` question category
        - remove `scienceQuestions` data structure
        - initialize `Sports` questions into the new `questions` data structure
        - Use the new structure for `Sports` questions at `totalNumQuestions` method
        - `extract constat` `Sports` category question
        - Use the new structure for `Sports` questions at `currentQuestion` method
        - Remove `sportQuestions` data structure
        - Initialize `Rock` questions at the new data structure