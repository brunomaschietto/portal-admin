.PHONY: test test-unit test-watch test-mock

test:
	php tests/run_tests.php

test-unit:
	php tests/run_tests.php

test-mock:
	php tests/run_tests.php

test-watch:
	watch -n 2 php tests/run_tests.php

test-clean:
	@echo "Limpando dados em memória (nada a fazer - já é mockado!)"
