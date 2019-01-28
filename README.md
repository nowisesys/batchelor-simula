# Simulation application for batchelor

Tests application that simulates some system load and random otuput from running
the simula program (see SETUP). This application is intended to demonstrate how
to setup task processing.

Symbolic links are used to save space, but a real world application would instead
copy all files from vendor.

These changes has been done:
----------------------------------

1. Task has been defined inside the source directory.

2. The process service has been modified (in file config/services.inc) to return
   task as default handler.

3. Settings for load simulation task was added in config/defaults.app
