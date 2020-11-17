import Translator from '@arcanescripts/translator'
import translations from '../translations'

const translator = new Translator({
    translations,
})

const getCurrentLocale = (): string => translator.getLocale();

const trans = (key: string, replacers?: Object): string => translator.get(key, replacers);

export {
    getCurrentLocale,
    trans,
}

export default translator
