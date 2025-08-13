# ANSI escape codes for colors
class ColorModel:
    RESET = "\033[0m"
    RED = "\033[91m"
    GREEN = "\033[92m"
    YELLOW = "\033[93m"
    BLUE = "\033[94m"
    MAGENTA = "\033[95m"
    CYAN = "\033[96m"

# print_colored("This is red text.", ColorModel.RED)
# print_colored("This is green text.", ColorModel.GREEN)
# print_colored("This is yellow text.", ColorModel.YELLOW)
# print_colored("This is blue text.", ColorModel.BLUE)
# print_colored("This is magenta text.", ColorModel.MAGENTA)
# print_colored("This is cyan text.", ColorModel.CYAN)

class Colors:
    def print_colored(text, color):
        print(color + text + ColorModel.RESET)