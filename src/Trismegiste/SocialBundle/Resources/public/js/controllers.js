var combatApp = angular.module('combatApp', []);

combatApp.controller('MainCtrl', function($scope, $http) {

    $scope.characters = [];

    $scope.currentInit = 100; // max init
    $scope.currentRound = 1;
    $scope.selected_char = {};

    $scope.template = [{name: 'empty'}];
    if (localStorage.hasOwnProperty('rpgraph/combat/template')) {
        $scope.template = angular.fromJson(localStorage.getItem('rpgraph/combat/template'))
    }

    $scope.select = function(name) {
        $scope.characters.forEach(function(item) {
            if (item.name === name) {
                $scope.selected_char = item;
            }
        });
    };

    function existCharacterName(name) {
        var flag = false;

        $scope.characters.forEach(function(item) {
            if (item.name === name) {
                flag = true;
                return;
            }
        })

        return flag;
    }

    $scope.addCharacter = function(name) {
        var selected = null;
        $scope.template.forEach(function(item) {
            if (item.name === name) {
                selected = angular.copy(item)
            }
        });

        var finalName = selected.name;
        var index = 1;

        while (existCharacterName(finalName)) {
            finalName = selected.name + index;
            index++;
        }
        selected.name = finalName;

        $scope.characters.push(selected);
    };

    $scope.getWoundMalus = function(perso) {
        if (!angular.isUndefined(perso)) {
            var idxMalus = perso.wound / perso.earth;
            var woundedMalus = [3, 5, 10, 15, 20, 40, 'out', 'dead'];

            if (idxMalus <= 5) {
                return 0;
            } else {
                var rank = Math.ceil((idxMalus - 5) / 2) - 1;
                return woundedMalus[rank];
            }
        }
    };

    $scope.getHP = function(perso) {
        if (!angular.isUndefined(perso)) {
            return perso.earth * (5 + 7 * 2);
        }
    };

    $scope.getHilite = function(stat) {
        return (stat === $scope.currentInit) ? "current-turn" : '';
    };

    $scope.goToNextTurn = function() {
        var newInit = 0;
        $scope.characters.forEach(function(item) {
            if (item.init < $scope.currentInit) {
                if (item.init > newInit) {
                    newInit = item.init;
                }
            }
        })
        $scope.currentInit = newInit;
    };

    $scope.goToNextRound = function() {
        if ($scope.currentInit === 0) {
            $scope.currentRound++;
            $scope.currentInit = 100;
            $scope.goToNextTurn();
        }
    };

    $scope.isSelected = function(name) {
        return (name === $scope.selected_char.name) ? "selected-character" : '';
    };

    $scope.hasSelection = function() {
        return !angular.isUndefined($scope.selected_char.name);
    };

    $scope.attackRoll = function(p) {
        try {
            $scope.attackRollResult = rollAndKeep(p.attack.roll, p.attack.keep) - $scope.getWoundMalus((p));
        } catch (e) {
            console.log(e);
            $scope.attackRollResult = 'N/A';
        }
    };

    $scope.damageRoll = function(p) {
        try {
            $scope.damageRollResult = rollAndKeep(p.damage.roll, p.damage.keep);
        } catch (e) {
            console.log(e);
            $scope.damageRollResult = 'N/A';
        }
    };

    function oneD10() {
        return Math.ceil(Math.random() * 10);
    }

    function explodingD10() {
        var res = 0;
        var dice;
        do {
            dice = oneD10();
            res += dice;
        } while (dice === 10);

        return res;
    }

    function rollAndKeep(roll, keep) {
        if (!((roll > 0) && (keep > 0))) {
            return 0;
        }

        if (keep > roll) {
            keep = roll;
        }

        var res = 0;
        var tirage = [];
        for (var i = 0; i < roll; i++) {
            tirage.push(explodingD10());
        }
        console.log(tirage);
        tirage.sort(function(a, b) {
            return a < b;
        });

        for (var i = 0; i < keep; i++) {
            res += tirage[i];
        }

        return res;
    }

    $scope.persist = function() {
        localStorage.setItem('rpgraph/combat/state', angular.toJson({
            listing: $scope.characters,
            round: $scope.currentRound,
            init: $scope.currentInit
        }));
    };

    $scope.restore = function() {
        var state = angular.fromJson(localStorage.getItem('rpgraph/combat/state'));

        $scope.characters = state.listing;
        $scope.currentRound = state.round;
        $scope.currentInit = state.init;

    };

    $scope.addWoundToSelected = function(val) {
        var reduc = 0;
        if (!angular.isUndefined($scope.selected_char.damageReduction)) {
            reduc = $scope.selected_char.damageReduction;
        }
        if (angular.isUndefined($scope.selected_char.wound)) {
            $scope.selected_char.wound = 0;
        }
        $scope.selected_char.wound += Math.max(0, val + reduc);
        $scope.addedWoundsValue = 0;
    };

    $scope.setKilled = function(p) {
        p.wound = $scope.getHP(p) + 1
        p.init = 0
    }

    $scope.deleteCharacter = function(p) {
        $scope.characters.forEach(function(item, index) {
            if (item.name === p.name) {
                $scope.characters.splice(index, 1)
                if ($scope.selected_char.name === p.name) {
                    $scope.selected_char = {}
                }
                return;
            }
        })
    }
    
    $scope.duplicateCharacter = function(p) {
        var selected = angular.copy(p);

        var finalName = selected.name;
        var index = 1;

        while (existCharacterName(finalName)) {
            finalName = selected.name + index;
            index++;
        }
        selected.name = finalName;
        $scope.characters.push(selected)
    };

    $scope.deleteTemplateByName = function(name) {
        $scope.template.forEach(function(item, index) {
            if (item.name === name) {
                $scope.template.splice(index, 1)
            }
        })

        storeAllTemplate()
    }

    $scope.saveAsTemplate = function(chara) {
        var found = false

        $scope.template.forEach(function(item, index) {
            if (item.name === chara.name) {
                $scope.template[index] = angular.copy(chara)
                found = true
                return
            }
        })

        if (!found) {
            $scope.template.push(angular.copy(chara))
        }

        storeAllTemplate()
    }

    function storeAllTemplate() {
        localStorage.setItem('rpgraph/combat/template', angular.toJson($scope.template))
    }

});


