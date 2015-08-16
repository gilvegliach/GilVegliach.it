The basics of bit manipulation
==============================

Talking with fellow developers, I realized that many feel confused when it comes down to bit manipulation. It is indeed something not used on a day-to-day basis, but nevertheless the Android framework relies on it heavily in for memory optimizations: when a boolean can do, a bit can do too. Examples are View and Window flags. This post sets out to demystify the basics of bit manipulation: afterwards it will feel no more difficult than using arrays. 

First, before manipulating bits, we need containers for them: let's use plain Java ints for simplicity. For each int we have 32 positions to store bits. We will call 0 the least significant bit position and 31 the most significant one. Thinking bits as boolean elements in a container boils down bit problems to array problems: we only need to find out how to read and write such elements. We will write two methods, `get()` and `set()`, that given an int-container read and write respectively one of its elements.

    static int get(int n, int i) {
        int shifted = n >> i;
        return shifted & 1;  // 0 or 1
    }
 
The first line of `get()` divides n by 2^i (assuming n >= 0). If you think n in binary, this lops off the i least significant digits, filling in i zeros on the right. Example:

    0b01001 >> 2 == 0b01 (rightmost 01 is lopped off)

The second line of `get()` is equivalent to `shifted % 2` (assuming n >= 0). Think again `shifted` in binary: all digits of `shifted` will be 'and'-ed with a 0 except the least significant bit which will be 'and'-ed with a 1. This means everything will be zero except the least significant bit that will its value: we effectively conserved only the information about the parity of `shifted`. Now let's see the `set()` method.

    static int set(int n, int i, int val) {
        return val == 0 ? clear(n, i) : set(n, i);
    }
 
    private static int clear(int n, int i) {
        int mask = ~(1 << i);
        return n & mask;
    }

    private static int set(int n, int i) {
        int mask = 1 << i;
        return n | mask;
    }

We start splitting the general `set()` in two private methods, `clear()` and `set()`. The english espressions set and clear flag are standard jargon for setting a bit to 1 and clearing a bit to 0, and are used in Android too. First, consider the method `clear()`: we create a mask of the type 00..010..0 with a left shift, then we flip it to 11..101..1 by a bitwise negation. When we 'and' n with this mask we get a number which is the same as n, except having a 0 in the same place of the 0 in the mask: we cleared only one bit.

Now it is the turn of (private) `set()`: first, differently from arrays, ints are primitives, so we cannot hold a reference from outside and see the write: we need to create a new int, with the wanted bit set, and return it. Otherwise, the logic is similar to `get()`, except this time the mask looks like 00..010..0 and we 'or' it with n.
