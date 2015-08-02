Espresso: Click on last item in AdapterView
===========================================

In Espresso is quite easy to tap on the first element of an `AdapterView`, such
as a `ListView`. This can be easily done calling `DataIteraction.atPosition(0)`. 
Clicking on the last item though, is much more complicated. The last position is 
unknown to Espresso and extracting it stringing together a `findViewById()` and
`AdapterView.getCount()` seems to defeat the purpose of using Espresso 
altogether.

Luckily there is a simple solution to this problem: let Espresso see the items
in reversed order and then click on the first item. This can be accomplished
with a custom `AdapterViewProtocol`.

`AdapterViewProtocol` is a simple interface that defines how Espresso interacts
with `AdapterViews`. It supports four operations:

1. Get all data items in the adapter
2. Given a child view, return the corresponding data item
3. Given a data item, ask whether it is displayed by some child view
4. Force a data item to be displayed by a child view

For our use-case we just need to get the complete dataset and reverse it, then
it can be used like this:

    onData(instanceOf(MyAdapterItem.class))
        .atPosition(0)                                       
        .usingAdapterViewProtocol(new ReverseProtocol())
        .perform(click());

Here comes the code for the protocol. Note that the standard protocol is a
private class so it can't be extended, so we delegate to it: 

    public class ReverseProdocol implements AdapterViewProtocol {
        private final AdapterViewProtocol delegate = standardProtocol();

        @Override
        public Iterable<AdaptedData> getDataInAdapterView(
                AdapterView<? extends Adapter> adapterView) {
            LinkedList<AdaptedData> result = new LinkedList<>();
            for (AdaptedData data : delegate.getDataInAdapterView(adapterView)) {
                result.addFirst(data);
            }
            return result;
        }

        @Override
        public Optional<AdaptedData> getDataRenderedByView(
                AdapterView<? extends Adapter> adapterView, View view) {
            return delegate.getDataRenderedByView(adapterView, view);
        }

        // Similarly delegate to the other two methods
        // ...
    }
