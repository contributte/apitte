.PHONY: install qa cs csf phpstan tests coverage

install:
	composer update

qa: phpstan cs

cs:
ifdef GITHUB_ACTION
	vendor/bin/phpcs --standard=ruleset.xml --encoding=utf-8 --colors -nsp -q --report=checkstyle src tests | cs2pr
else
	vendor/bin/phpcs --standard=ruleset.xml --encoding=utf-8 --colors -nsp src tests
endif

csf:
	vendor/bin/phpcbf --standard=ruleset.xml --encoding=utf-8 --colors -nsp src tests

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

tests:
	vendor/bin/tester -s -p php --colors 1 -C -d memory_limit=512M tests/Cases

coverage:
ifdef GITHUB_ACTION
	vendor/bin/tester -s -p phpdbg --colors 1 -C -d memory_limit=512M --coverage coverage.xml --coverage-src src tests/Cases
else
	vendor/bin/tester -s -p phpdbg --colors 1 -C -d memory_limit=512M --coverage coverage.html --coverage-src src tests/Cases
endif
