import {createReduxStore, register} from '@wordpress/data';

const DEFAULT_STATE = {
	schema: null,
	faqItems: [],
}

const store = createReduxStore('starboard/faq-store', {
	reducer(state = DEFAULT_STATE, action) {
		switch (action.type) {
			case 'ADD_OR_UPDATE_FAQ_ITEM':
				return {
					...state,
					faqItems: [
						...state.faqItems.filter((item) => item.id !== action.id),
						{id: action.id, ...action.data},
					],
				};
			case 'REMOVE_FAQ_ITEM':
				return {
					...state,
					faqItems: state.faqItems.filter((item) => item.id !== action.id),
				};
			default:
				return state;
		}
	},
	actions: {
		addOrUpdateFaqItem(id, data) {
			return {
				type: 'ADD_OR_UPDATE_FAQ_ITEM',
				id,
				data,
			};
		},
		removeFaqItem(id) {
			return {
				type: 'REMOVE_FAQ_ITEM',
				id,
			};
		},
	},
	selectors: {
		getFaqItems(state) {
			return state.faqItems;
		},
	},
	resolvers: {},
});

register(store)
