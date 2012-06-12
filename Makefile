com_skroutzeasy.zip:
	zip -r com_skroutzeasy.zip . -x @.zipignore

.PHONY: clean

clean:
	rm -f com_skroutzeasy.zip
